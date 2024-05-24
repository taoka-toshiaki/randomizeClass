<?php
// 頻繁にランダム番号が重複しないようにするクラス.
class randomizeClass
{
    /**
     * ランダムに配列のインデックス番号を取得する
     */
    public function getRandomIndex($hasArray,$index,$filename='rand.dat',$max=15)
    {
        try {

            //過去のランダム番号を保存しているファイルが無ければ空ファイルを作成する
            if(!file_exists($filename)){                
                file_put_contents($filename,'');
            }

            //過去のランダム番号を保存しているデータを取得
            $fileData = file_get_contents($filename);

            //下記の場合は処理しない
            if ($fileData === false || count($hasArray) < $max) {
                return $index;
            }
            //ファイルデータをカンマで配列に分離
            $isArray = explode(',',$fileData);

            //過去のランダム番号に存在しないか？
            if (array_search($index,$isArray,false) === false) {
                //ランダム格納前処理
                array_unshift($isArray,$index);
                if (count($isArray)>$max) {
                    array_pop($isArray);
                }
                array_filter($isArray,function($val){
                    return $val !=='';
                });
                //ランダム番号を格納
                file_put_contents($filename,implode(',',$isArray));

                return $index;
            }
            //過去のランダム番号に合致したため再帰処理を行う．
            return $this->getRandomIndex($hasArray,array_rand($hasArray),$filename,$max);

        } catch (\Throwable $th) {
            throw $th;
            echo $th->getMessage();
        }
    }
}

//テスト用配列を生成
$hasArray = (function(){
    $val = [];
    for($i=0;$i<100;$i++){
        $val[] = $i;
    }
    return $val;
})();
$filename = 'rand.dat';
//ランダム番号::配列インデックスを取得し表示
print (new randomizeClass)->getRandomIndex($hasArray,array_rand($hasArray),$filename);
print PHP_EOL;
//過去のランダム番号を保存しているデータを取得し表示
print file_get_contents($filename);
print PHP_EOL;
