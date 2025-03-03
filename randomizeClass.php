<?php
// 頻繁にランダム番号が重複しないようにするクラス
class randomizeClass
{
    /**
     * ランダムに配列のインデックス番号を取得する
     */
    public function getRandomIndex(array $array, int $index, string $filename = 'rand.dat', int $max = 15): int
    {
        try {
            // 過去のランダム番号を保存しているファイルが無ければ空ファイルを作成する
            if (!file_exists($filename)) {
                file_put_contents($filename, '');
            }

            // 過去のランダム番号を取得
            $fileData = file_get_contents($filename);

            // データ取得失敗や配列サイズが max より小さい場合はそのまま返す
            if ($fileData === false || count($array) < $max) {
                return $index;
            }

            // カンマ区切りで配列化し、空文字を取り除く
            $history = array_filter(explode(',', $fileData), fn($val) => $val !== '');

            // 過去のランダム番号に存在しない場合
            if (!in_array($index, $history, false)) {
                // 履歴の先頭に追加し、最大数を超えたら削除
                array_unshift($history, $index);
                if (count($history) > $max) {
                    array_pop($history);
                }

                // 履歴を保存
                file_put_contents($filename, implode(',', $history));

                return $index;
            }

            // 重複していた場合、新しいランダム値を再取得（再帰処理）
            return $this->getRandomIndex($array, array_rand($array, 1), $filename, $max);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

// テスト用配列を生成
$array = range(0, 99);
$filename = 'rand.dat';

// ランダム番号::配列インデックスを取得し表示
echo (new randomizeClass())->getRandomIndex($array, array_rand($array, 1), $filename) . PHP_EOL;

// 過去のランダム番号を保存しているデータを取得し表示
echo file_get_contents($filename) . PHP_EOL;
