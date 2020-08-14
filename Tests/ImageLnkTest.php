<?php

use PHPUnit\Framework\TestCase;

require_once sprintf('%s/../lib/ImageLnk.php', dirname(__FILE__));

class ImageLnkTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        ImageLnk_Config::set('cache_directory', 'tmp');
        ImageLnk_Config::set('cache_expire_minutes', 30);
    }

    private function checkResponse($url, $title, $imageurls, $referer = null, $backlink = null)
    {
        $response = ImageLnk::getImageInfo($url);
        if (!$response) {
            throw new Exception("response is null: $url");
        }

        $expect = $title;
        $actual = $response->getTitle();
        $similar = 0;
        similar_text($expect, $actual, $similar);
        if ($similar < 95) {
            print "\n";
            print "expect: $expect\n";
            print "actual: $actual\n";
            $this->fail();
        }

        $expect = $imageurls;
        $actual = $response->getImageURLs();
        $this->assertSame(count($expect), count($actual));
        for ($i = 0; $i < count($expect); ++$i) {
            if (preg_match('/^regex:(.+)/', $expect[$i], $matches)) {
                $regex = $matches[1];
                if (!preg_match($regex, $actual[$i])) {
                    print "\n";
                    print "expect: $regex\n";
                    print 'actual: ' . $actual[$i] . "\n";
                    $this->fail();
                }
            } else {
                $this->assertSame($expect[$i], $actual[$i]);
            }
        }

        if ($referer == null) {
            $referer = $url;
        }
        $expect = $referer;
        $actual = $response->getReferer();
        $this->assertSame($expect, $actual);

        if ($backlink !== null) {
            $expect = $backlink;
            $actual = $response->getBackLink();
            $this->assertSame($expect, $actual);
        }
    }

    // ======================================================================
    public function testAmeblo1()
    {
        $url = 'https://ameblo.jp/hakasetoiu-ikimono/image-10430643614-10370336976.html';
        $title = '第４話：Beautiful nameの画像';
        $imageurls = [
            'https://stat.ameba.jp/user_images/20100109/22/hakasetoiu-ikimono/5f/c7/j/o0360050010370336976.jpg?cat=136',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    public function testAmeblo2()
    {
        $url = 'https://s.ameblo.jp/hakasetoiu-ikimono/image-10430643614-10370336976.html';
        $title = '第４話：Beautiful nameの画像';
        $imageurls = [
            'https://stat.ameba.jp/user_images/20100109/22/hakasetoiu-ikimono/5f/c7/j/o0360050010370336976.jpg?cat=136',
        ];
        $referer = 'https://ameblo.jp/hakasetoiu-ikimono/image-10430643614-10370336976.html';
        $this->checkResponse($url, $title, $imageurls, $referer);
    }

    public function testAmeblo3()
    {
        $url = 'https://ameblo.jp/strawberry-ayana/image-10963873926-11370958832.html';
        $title = 'わんふぇす2の画像';
        $imageurls = [
            'https://stat.ameba.jp/user_images/20110724/19/strawberry-ayana/ac/1e/j/o0480064011370958832.jpg?cat=136',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function testAscii1()
    {
        $url = 'https://ascii.jp/elem/000/003/023/3023705/img.html';
        $title = '拡大画像【ASCII.jp】';
        $imageurls = [
            'https://ascii.jp/img/2020/03/20/3023705/o/33a20cff19c74187.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function testAkibablog1()
    {
        $url = 'http://node3.img3.akibablog.net/11/may/1/real-qb/119.html';
        $title = '[画像]:ゲーマーズ本店にリアルキュゥべえ　「どうみても不審者ｗｗｗ」';
        $imageurls = [
            'http://node3.img3.akibablog.net/11/may/1/real-qb/119.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function testHatena1()
    {
        $url = 'https://f.hatena.ne.jp/tekezo/20090625215759';
        $title = 'タイトルです。';
        $imageurls = [
            'https://cdn-ak.f.st-hatena.com/images/fotolife/t/tekezo/20090625/20090625215759.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function testDengeki1()
    {
        $url = 'https://dengekionline.com/elem/000/000/364/364901/img.html';
        $title = '電撃 - 【App通信】iPad 2が満を持して発売！ 美少女姉妹による萌え系紙芝居 アプリも';
        $imageurls = [
            'https://ssl.dengeki.com/elem/000/000/364/364922/c20110502_app_th_o_.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function testItmedia1()
    {
        $url = 'https://image.itmedia.co.jp/l/im/pcuser/articles/1502/07/l_og_akibatokka_001.jpg';
        $title = '128GバイトSSDが7500円切り！ 256Gバイトも1万2000円弱に';
        $imageurls = [
            'https://image.itmedia.co.jp/pcuser/articles/1502/07/l_og_akibatokka_001.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    public function testItmedia2()
    {
        $url = 'https://image.itmedia.co.jp/l/im/nl/articles/1106/02/l_ky_robo_0602_5.jpg';
        $title = 'セグウェイが歩道を走る　つくばでロボットの公道走行実験スタート';
        $imageurls = [
            'https://image.itmedia.co.jp/nl/articles/1106/02/l_ky_robo_0602_5.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    public function testItmedia3()
    {
        $url = 'https://image.itmedia.co.jp/l/im/mobile/articles/1110/14/l_os_appsomm03.jpg';
        $title = '“普通の女性”目線で厳選したスマートフォンアプリ紹介サイト「アプリソムリエ」';
        $imageurls = [
            'https://image.itmedia.co.jp/mobile/articles/1110/14/l_os_appsomm03.jpg',
        ];
        $backlink = 'https://www.itmedia.co.jp/mobile/articles/1110/14/news142.html#l_os_appsomm03.jpg';

        $this->checkResponse($url, $title, $imageurls, null, $backlink);
    }

    // ======================================================================
    public function testNicovideo1()
    {
        $url = 'https://www.nicovideo.jp/watch/sm17606436';
        $title = '【折り紙】バラを折ってみた';
        $imageurls = [
            'https://img.cdn.nimg.jp/s/nicovideo/thumbnails/17606436/17606436.original/r1280x720l?key=7fabfe53a977d05d842996d437bc6a25c235545cd77b69674c8cb1a29a2c41ca',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function testPixiv1()
    {
        // Image (medium)

        $url = 'https://www.pixiv.net/artworks/51691307';
        $title = 'Image Example';
        $imageurls = [
            'https://i.pximg.net/c/600x1200_90/img-master/img/2015/07/30/22/16/27/51691307_p0_master1200.jpg',
        ];
        $referer = 'https://www.pixiv.net/artworks/51691307';
        $this->checkResponse($url, $title, $imageurls, $referer);
    }

    public function testPixiv2()
    {
        // Manga

        $url = 'https://www.pixiv.net/member_illust.php?mode=medium&illust_id=51691357';
        $title = 'Manga Example';
        $imageurls = [
            'https://i.pximg.net/c/600x1200_90/img-master/img/2015/07/30/22/18/43/51691357_p0_master1200.jpg',
            'https://i.pximg.net/c/600x1200_90/img-master/img/2015/07/30/22/18/43/51691357_p1_master1200.jpg',
        ];
        $referer = 'https://www.pixiv.net/artworks/51691357';
        $this->checkResponse($url, $title, $imageurls, $referer);
    }

    public function testPixiv3()
    {
        // touch

        $url = 'https://touch.pixiv.net/member_illust.php?mode=medium&illust_id=51691307';
        $title = 'Image Example';
        $imageurls = [
            'https://i.pximg.net/c/600x1200_90/img-master/img/2015/07/30/22/16/27/51691307_p0_master1200.jpg',
        ];
        $referer = 'https://www.pixiv.net/artworks/51691307';
        $this->checkResponse($url, $title, $imageurls, $referer);
    }

    public function testPixiv4()
    {
        // Image (medium)

        $url = 'https://www.pixiv.net/member_illust.php?mode=medium&illust_id=51691307';
        $title = 'Image Example';
        $imageurls = [
            'https://i.pximg.net/c/600x1200_90/img-master/img/2015/07/30/22/16/27/51691307_p0_master1200.jpg',
        ];
        $referer = 'https://www.pixiv.net/artworks/51691307';
        $this->checkResponse($url, $title, $imageurls, $referer);
    }

    // ======================================================================
    public function testYoutube1()
    {
        $url = 'https://www.youtube.com/watch?v=Tlmho7SY-ic&feature=player_embedded';
        $title = 'YouTube Turns Five!';
        $imageurls = [
            'https://i.ytimg.com/vi/Tlmho7SY-ic/maxresdefault.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function testPhotozou1()
    {
        $url = 'http://photozou.jp/photo/show/744707/79931926';
        $response = ImageLnk::getImageInfo($url);

        $title = 'テスト裏にキュゥべぇ描いた... - 写真共有サイト「フォト蔵」';
        $actual = $response->getTitle();
        $this->assertSame($title, $actual);

        $referer = 'http://photozou.jp/photo/photo_only/744707/79931926';
        $actual = $response->getReferer();
        $this->assertSame($referer, $actual);

        foreach ($response->getImageURLs() as $imageurl) {
            $expect = 1;
            $actual = preg_match('/^http:\/\/art22\.photozou\.jp\/bin\/photo\/79931926\/org/', $imageurl);
            $this->assertSame($expect, $actual);
        }
    }

    public function testPhotozou2()
    {
        $url = 'http://photozou.jp/photo/photo_only/744707/79931926?size=450';
        $response = ImageLnk::getImageInfo($url);

        $title = 'テスト裏にキュゥべぇ描いた... - 写真共有サイト「フォト蔵」';
        $actual = $response->getTitle();
        $this->assertSame($title, $actual);

        $referer = 'http://photozou.jp/photo/photo_only/744707/79931926';
        $actual = $response->getReferer();
        $this->assertSame($referer, $actual);

        foreach ($response->getImageURLs() as $imageurl) {
            $expect = 1;
            $actual = preg_match('/^http:\/\/art22\.photozou\.jp\/bin\/photo\/79931926\/org/', $imageurl);
            $this->assertSame($expect, $actual);
        }
    }

    // ======================================================================
    public function testImpress1()
    {
        $url = 'https://game.watch.impress.co.jp/img/gmw/docs/448/930/html/psn01.jpg.html';
        $title = '[拡大画像] SCEJ、PlayStation NetworkとQriocityのサービスを5月28日から再開。安全管理措置を導入し、ゲームコンテンツの無償提供も';
        $imageurls = [
            'https://game.watch.impress.co.jp/img/gmw/docs/448/930/psn01.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    public function testImpress2()
    {
        $url = 'https://dc.watch.impress.co.jp/img/dcw/docs/422/882/html/009.jpg.html';
        $title = '[画像] 写真で見る写真で見るカシオ「TRYX」（β機）(9/31) - デジカメ Watch Watch';
        $imageurls = [
            'https://dc.watch.impress.co.jp/img/dcw/docs/422/882/009.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    public function testImpress3()
    {
        $url = 'https://k-tai.watch.impress.co.jp/img/ktw/docs/460/236/html/sanzo.jpg.html';
        $title = '[拡大画像]サンヨーホームズ、Android採用のロボットを搭載した住宅(1/2) -  ケータイ Watch';
        $imageurls = [
            'https://k-tai.watch.impress.co.jp/img/ktw/docs/460/236/sanzo.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    public function testImpress4()
    {
        $url = 'https://akiba-pc.watch.impress.co.jp/hotline/20110806/image/kfrety4.html';
        $title = '[拡大画像]お買い得価格情報 - AKIBA PC Hotline!';
        $imageurls = [
            'https://akiba-pc.watch.impress.co.jp/hotline/20110806/image/kfrety4.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function testTumblr1()
    {
        $url = 'https://titlebot.tumblr.com/post/5544499061/%E3%83%8D%E3%82%B3%E3%81%A8%E5%92%8C%E8%A7%A3%E3%81%9B%E3%82%88';
        $title = 'titlebot';
        $imageurls = [
            'regex:#https://\d+.media.tumblr.com/tumblr_llal1ttZ7W1qfqa6no1_400.jpg#',
        ];
        $referer = 'https://titlebot.tumblr.com/post/5544499061/%E3%83%8D%E3%82%B3%E3%81%A8%E5%92%8C%E8%A7%A3%E3%81%9B%E3%82%88';
        $this->checkResponse($url, $title, $imageurls, $referer);
    }

    public function testTumblr2()
    {
        $url = 'https://maegamipattun.tumblr.com/post/7815975799';
        $title = '前髪ぱっつん専用タンブラー';
        $imageurls = [
            'regex:#https://\d+.media.tumblr.com/tumblr_l1n113Lyub1qaxrtko1_640.jpg#',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function testWikipedia1()
    {
        $url = 'https://en.wikipedia.org/wiki/File:PANSDeinonychus.JPG';
        $title = 'English: Deinonychus antirrhopus skeleton, Philadelphia Academy of Natural Sciences';
        $imageurls = [
            'https://upload.wikimedia.org/wikipedia/commons/e/e6/PANSDeinonychus.JPG',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function testInstagram1()
    {
        $url = 'https://www.instagram.com/p/6XQ5nTTdjq/';
        $title = '@imagelnk on Instagram: “pepper”';
        $imageurls = [
            'regex:#https://.+.cdninstagram.com/.+?/t51.2885-15/e35/11356624_508726059287524_1160649839_n\.jpg?.+#',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    public function testInstagram2()
    {
        $url = 'https://instagram.com/p/6XQ5nTTdjq/';
        $title = '@imagelnk on Instagram: “pepper”';
        $imageurls = [
            'regex:#https://.+.cdninstagram.com/.+?/t51.2885-15/e35/11356624_508726059287524_1160649839_n\.jpg?.+#',
        ];
        $referer = 'https://www.instagram.com/p/6XQ5nTTdjq/';
        $this->checkResponse($url, $title, $imageurls, $referer);
    }

    // ======================================================================
    public function testNatalie1()
    {
        $url = 'https://natalie.mu/comic/gallery/news/50403/80332';
        $title = '物語の舞台である、由比ヶ浜へ連れ出してみるのも一興。(C)安部真弘（週刊少 年チャンピオン）／海の家れもん [画像ギャラリー 3/6] - コミックナタリー';
        $imageurls = [
            'https://ogre.natalie.mu/media/news/comic/2011/0531/ika_roke1.jpg?imwidth=750',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    public function testNatalie2()
    {
        $url = 'https://natalie.mu/music/gallery/news/50476/80357';
        $title = '新しくなった怒髪天のロゴ。 [画像ギャラリー 1/2] - 音楽ナタリー';
        $imageurls = [
            'https://ogre.natalie.mu/media/news/music/2011/0601/dohatsuten_topB.jpg?imwidth=750',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function test4gamer1()
    {
        $url = 'https://www.4gamer.net/games/044/G004471/20110616072/screenshot.html?num=003';
        $title = '画像集/「Wizardry Online」のCBT「機能テスト」先行体験プレイレポートを掲載。実態はやはり高難度……しかし序盤は「ロスト」の心配無用？';
        $imageurls = [
            'https://www.4gamer.net/games/044/G004471/20110616072/SS/003.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    public function test4gamer2()
    {
        $url = 'https://www.4gamer.net/games/044/G004471/20110616072/screenshot.html';
        $title = '画像集/「Wizardry Online」のCBT「機能テスト」先行体験プレイレポートを掲載。実態はやはり高難度……しかし序盤は「ロスト」の心配無用？';
        $imageurls = [
            'https://www.4gamer.net/games/044/G004471/20110616072/SS/001.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function testZakzak1()
    {
        $url = 'https://www.zakzak.co.jp/gravure/idol/photos/20110627/idl1106271244001-p1.htm';
        $title = '１６歳の森野朝美、スレンダーボディー炸裂にドキッ  - グラビアアイドル - ZAKZAK';
        $imageurls = [
            'https://www.zakzak.co.jp/gravure/idol/images/20110627/idl1106271244001-p1.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    public function testZakzak2()
    {
        $url = 'https://www.zakzak.co.jp/sports/etc_sports/photos/20150808/spo1508081531009-p1.htm';
        $title = '松山、４９位に後退　池ポチャ２度の大乱調　ブリヂストン招待  - スポーツ - ZAKZAK';
        $imageurls = [
            'https://www.zakzak.co.jp/sports/etc_sports/images/20150808/spo1508081531009-p1.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function testUncyclopediaJa1()
    {
        $url = 'https://ja.uncyclopedia.info/wiki/%E3%83%95%E3%82%A1%E3%82%A4%E3%83%AB:CaesiumSan_01.jpg';
        $title = 'ファイル:CaesiumSan 01.jpg - アンサイクロペディア';
        $imageurls = [
            'https://images.uncyc.org/ja/2/25/CaesiumSan_01.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    public function testUncyclopediaJa2()
    {
        $url = 'https://ja.uncyclopedia.info/wiki/%E3%83%95%E3%82%A1%E3%82%A4%E3%83%AB:Sigeru.jpg';
        $title = 'ファイル:Sigeru.jpg - アンサイクロペディア';
        $imageurls = [
            'https://images.uncyc.org/ja/1/11/Sigeru.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    public function testUncyclopediaJa3()
    {
        $url = 'https://ansaikuropedia.org/wiki/%E3%83%95%E3%82%A1%E3%82%A4%E3%83%AB:GRP_0258.JPG';
        $title = 'ファイル:GRP 0258.JPG - アンサイクロペディア';
        $imageurls = [
            'https://images.uncyc.org/ja/f/f4/GRP_0258.JPG',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function testNewsLivedoorCom1()
    {
        $url = 'https://news.livedoor.com/article/image_detail/5786423/?img_id=2118390';
        $title = '【画像】【こんにちは！ナマな人々】露出度高めなコスプレ娘・知羽音さん 1/2 - ライブドアニュース';
        $imageurls = [
            'https://image.news.livedoor.com/newsimage/c/0/c08fd40e8bba4eee8ed91b72707e0378.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    public function testNewsLivedoorCom2()
    {
        $url = 'https://news.livedoor.com/article/image_detail/5460430/?img_id=1848550';
        $title = '【画像】美少女時計に人気モデル 前田希美 黒田瑞貴 志田友美が登場 ウェブ版もスタート 17/18 - ライブドアニュース';
        $imageurls = [
            'https://image.news.livedoor.com/newsimage/0/1/01e1a_756_b0853.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function testTwitter1()
    {
        $url = 'https://twitter.com/tekezo/status/474587873925017600';
        $title = 'twitter: Takayama Fumihiko: KeyRemap4MacBook v9.99.11 or later allows you to change keys only when you are editing text (or not). http://t.co/WnuoqQxDRW';
        $imageurls = [
            'http://pbs.twimg.com/media/BpYTJ-iIcAARRVg.png:large',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    public function testTwitter2()
    {
        $url = 'https://mobile.twitter.com/tekezo/status/474587873925017600';
        $title = 'twitter: Takayama Fumihiko: KeyRemap4MacBook v9.99.11 or later allows you to change keys only when you are editing text (or not). http://t.co/WnuoqQxDRW';
        $imageurls = [
            'http://pbs.twimg.com/media/BpYTJ-iIcAARRVg.png:large',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function testCnetJp1()
    {
        $url = 'https://japan.cnet.com/article/35008398/14/';
        $title = 'NEC PCができるまで--山形の米沢事業所を訪問 - (page 14) - CNET Japan: 　シートを重ねるだけでキートップの相違を検出できる「発見くん」。これは数字の「0」とアルファベットの「O」が誤って取り付けられた例だ。工場には、トヨタ生産方式で知られる「ニンベンの付いた自働化」があらゆるところに取り入れられている。';
        $imageurls = [
            'https://japan.cnet.com/storage/2011/09/30/9d06e8a0497f76a9d42bdbce397e2796/110930necr9138308.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    public function testCnetJp2()
    {
        $url = 'https://japan.cnet.com/image/l/storage/35004965/storage/2011/07/07/6158a520ae3ce67be4959c9b8cf62e72/20110707_casio_06.jpg';
        $title = 'CNET Japan: レンズ部を中心にフレームは360度回転し、モニタも270度回転する';
        $imageurls = [
            'https://japan.cnet.com/storage/2011/07/07/6158a520ae3ce67be4959c9b8cf62e72/20110707_casio_06.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    public function testCnetJp3()
    {
        $url = 'https://japan.cnet.com/digital/camera/35004965/';
        $expect = null;
        $actual = ImageLnk::getImageInfo($url);
        $this->assertSame($expect, $actual);
    }

    // ======================================================================
    public function testCookpad1()
    {
        $url = 'https://cookpad.com/recipe/720203';
        $title = '大根とツナとホタテのサラダ♪ by ともにゃんママ [クックパッド] 簡単おいしいみんなのレシピが256万品';
        $imageurls = [
            'https://img.cpcdn.com/recipes/720203/840x1461s/0faabd4907e35e0c7544d50d8bba9a14?u=887658&p=1232792798',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function testMynavi1()
    {
        $url = 'https://news.mynavi.jp/photo/article/20111108-a008/images/005l.jpg';
        $title = '拡大画像 005 | サイボウズ、クラウド基盤「cybozu.com」の運用を開始しPaaSを提供 | マイナビニュース';
        $imageurls = [
            'https://news.mynavi.jp/article/20111108-a008/images/005l.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    public function testMynavi2()
    {
        $url = 'https://news.mynavi.jp/photo/article/20110307-appinventor/images/006l.jpg';
        $title = '拡大画像 006l | 経験ゼロでも大丈夫!? App Inventorで始めるAndroidアプリ開発 (1) まずは稼働環境を整備 | マイナビニュース';
        $imageurls = [
            'https://news.mynavi.jp/article/20110307-appinventor/images/006l.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function testStocksFinanceYahooCoJp()
    {
        $url = 'https://stocks.finance.yahoo.co.jp/stocks/detail/?code=3656.T&d=1m';
        $title = 'ＫＬａｂ(株)【3656】：株式/株価 - Yahoo!ファイナンス';
        $imageurls = [
            'https://chart.yahoo.co.jp/?code=3656.T&tm=1m&size=e',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function testSankakucomplex1()
    {
        $url = 'https://chan.sankakucomplex.com/post/show/5949839/';
        $title = 'Post 5949839';
        $imageurls = [
            'regex:#https://cs\.sankakucomplex\.com/data/sample/2e/54/sample-2e540481ae41d3b9d652f1ac92a82b5c\.jpg\?.*#',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    public function testSankakucomplex2()
    {
        $url = 'https://chan.sankakucomplex.com/ja/post/show/6135230';
        $title = 'Post 6135230';
        $imageurls = [
            'regex:#https://cs\.sankakucomplex\.com/data/sample/7b/44/sample-7b44e9025158c4e7fc0ca1cbaafa3e9d.jpg\?.*#',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }

    // ======================================================================
    public function testDropbox1()
    {
        $url = 'https://www.dropbox.com/s/v8a95rih0ah0ej0/chart2.png?dl=0';
        $title = 'Dropbox - chart2 (1).png - Simplify your life';
        $imageurls = [
            'regex:#^https://[a-z0-9]+\.previews\.dropboxusercontent\.com/p/thumb/.+?/p\.png\?size=2048x1536&size_mode=3#',
        ];
        $referer = 'https://www.dropbox.com/s/v8a95rih0ah0ej0/chart2%20%281%29.png?dl=0';
        $this->checkResponse($url, $title, $imageurls, $referer);
    }

    // ======================================================================
    public function testImgur()
    {
        $url = 'https://imgur.com/gallery/GHlof1T';
        $title = 'klab';
        $imageurls = [
            'https://i.imgur.com/DghXdFe.jpg',
        ];
        $this->checkResponse($url, $title, $imageurls);
    }
}
