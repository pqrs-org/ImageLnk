<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

require_once sprintf('%s/../lib/ImageLnk.php', dirname(__FILE__));

class ImageLnkTest extends PHPUnit_Framework_TestCase
{
    function __construct()
    {
        ImageLnk_Config::set('cache_directory', 'tmp');
        ImageLnk_Config::set('cache_expire_minutes', 30);
    }

    private function check_response($url, $title, $imageurls, $referer = null, $backlink = null)
    {
        $response = ImageLnk::getImageInfo($url);
        if (! $response) {
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

    private function expect_false($url)
    {
        $expect = false;
        $actual = ImageLnk::getImageInfo($url);
        $this->assertSame($expect, $actual);
    }

    // ======================================================================
    function test_ameblo1()
    {
        $url = 'https://ameblo.jp/hakasetoiu-ikimono/image-10430643614-10370336976.html';
        $title = '第４話：Beautiful nameの画像 | 研究者マンガ「ハカセといふ生物」';
        $imageurls = [
            'https://stat.ameba.jp/user_images/20100109/22/hakasetoiu-ikimono/5f/c7/j/o0360050010370336976.jpg',
        ];
        $this->check_response($url, $title, $imageurls);
    }

    function test_ameblo2()
    {
        $url = 'https://s.ameblo.jp/hakasetoiu-ikimono/image-10430643614-10370336976.html';
        $title = '第４話：Beautiful nameの画像 | 研究者マンガ「ハカセといふ生物」';
        $imageurls = [
            'https://stat.ameba.jp/user_images/20100109/22/hakasetoiu-ikimono/5f/c7/j/o0360050010370336976.jpg',
        ];
        $referer = 'http://ameblo.jp/hakasetoiu-ikimono/image-10430643614-10370336976.html';
        $this->check_response($url, $title, $imageurls, $referer);
    }

    function test_ameblo3()
    {
        $url = 'https://ameblo.jp/strawberry-ayana/image-10963873926-11370958832.html';
        $title = 'わんふぇす2の画像 | 竹達彩奈オフィシャルブログ「Strawberry Candy」Powe…';
        $imageurls = [
            'https://stat.ameba.jp/user_images/20110724/19/strawberry-ayana/ac/1e/j/o0480064011370958832.jpg',
        ];
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_ascii1()
    {
        $url = 'http://ascii.jp/elem/000/001/013/1013475/img.html';
        $title = 'いよいよ来た！ Windows 10が「無償アップグレードの予約」を開始！';
        $imageurls = array(
            'http://ascii.jp/elem/000/001/013/1013475/01_814x528.png',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_ascii2()
    {
        $url = 'http://weekly.ascii.jp/elem/000/000/066/66769/';
        $title = 'ThinkPad Tabletに3G版が登場！　しかもSIMフリーだと!?';
        $imageurls = array(
            'http://weekly.ascii.jp/elem/000/000/066/66769/lenovo_sim_free03_cs1e1_x1000.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_ascii3()
    {
        $url = 'http://ascii.jp/elem/000/000/672/672411/img800.html';
        $title = '寒い日に寄り添ってぬくぬくする猫たち';
        $imageurls = array(
            'http://ascii.jp/elem/000/000/672/672411/DSC00017_800x.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_akibablog1()
    {
        $url = 'http://node3.img3.akibablog.net/11/may/1/real-qb/119.html';
        $title = '[画像]:ゲーマーズ本店にリアルキュゥべえ　「どうみても不審者ｗｗｗ」';
        $imageurls = array(
            'http://node3.img3.akibablog.net/11/may/1/real-qb/119.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_hatena1()
    {
        $url = 'http://f.hatena.ne.jp/tekezo/20090625215759';
        $title = 'タイトルです。';
        $imageurls = array(
            'http://cdn-ak.f.st-hatena.com/images/fotolife/t/tekezo/20090625/20090625215759.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_dengeki1()
    {
        $url = 'http://dengekionline.com/elem/000/000/364/364901/img.html';
        $title = '電撃 - 【App通信】iPad 2が満を持して発売！ 美少女姉妹による萌え系紙芝居 アプリも';
        $imageurls = array(
            'http://dengekionline.com/elem/000/000/364/364901/c20110502_app_18_cs1w1_347x720.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_twitpic1()
    {
        $url = 'http://twitpic.com/1yggai';
        $response = ImageLnk::getImageInfo($url);

        $title = '良くお休みのようで';
        $actual = $response->getTitle();
        $this->assertSame($title, $actual);

        $referer = 'http://twitpic.com/1yggai';
        $actual = $response->getReferer();
        $this->assertSame($referer, $actual);

        $this->assertSame(1, count($response->getImageURLs()));
    }

    function test_twitpic2()
    {
        $url = 'http://twitpic.com/c17ing';
        $response = ImageLnk::getImageInfo($url);

        $title = '総武線各駅停車、ホームに人が溢れ危険な状態だったので、諦めて総武線快速で東京に出ることにする。総武線快速乗ったらなぜか車内に鳥が……';
        $actual = $response->getTitle();
        $this->assertSame($title, $actual);

        $referer = 'http://twitpic.com/c17ing';
        $actual = $response->getReferer();
        $this->assertSame($referer, $actual);

        $this->assertSame(1, count($response->getImageURLs()));
    }

    // ======================================================================
    function test_itmedia1()
    {
        $url = 'http://image.itmedia.co.jp/l/im/pcuser/articles/1502/07/l_og_akibatokka_001.jpg';
        $title = '128GバイトSSDが7500円切り！ 256Gバイトも1万2000円弱に';
        $imageurls = array(
            'http://image.itmedia.co.jp/pcuser/articles/1502/07/l_og_akibatokka_001.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_itmedia2()
    {
        $url = 'http://image.itmedia.co.jp/l/im/nl/articles/1106/02/l_ky_robo_0602_5.jpg';
        $title = 'セグウェイが歩道を走る　つくばでロボットの公道走行実験スタート';
        $imageurls = array(
            'http://image.itmedia.co.jp/nl/articles/1106/02/l_ky_robo_0602_5.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_itmedia3()
    {
        $url = 'http://image.itmedia.co.jp/l/im/mobile/articles/1110/14/l_os_appsomm03.jpg';
        $title = '“普通の女性”目線で厳選したスマートフォンアプリ紹介サイト「アプリソムリエ」';
        $imageurls = array(
            'http://image.itmedia.co.jp/mobile/articles/1110/14/l_os_appsomm03.jpg',
        );
        $backlink = 'http://www.itmedia.co.jp/mobile/articles/1110/14/news142.html#l_os_appsomm03.jpg';

        $this->check_response($url, $title, $imageurls, null, $backlink);
    }

    // ======================================================================
    function test_nicovideo1()
    {
        $url = 'http://www.nicovideo.jp/watch/sm17606436';
        $title = '【折り紙】バラを折ってみた';
        $imageurls = array(
            'http://tn.smilevideo.jp/smile?i=17606436.L',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_pixiv1()
    {
        // Image (medium)

        $url = 'https://www.pixiv.net/member_illust.php?mode=medium&illust_id=51691307';
        $title = "「Image Example」/「imagelnk」のイラスト [pixiv]";
        $imageurls = array(
            'https://i.pximg.net/img-original/img/2015/07/30/22/16/27/51691307_p0.jpg',
        );
        $referer = 'https://www.pixiv.net/member_illust.php?mode=medium&illust_id=51691307';
        $this->check_response($url, $title, $imageurls, $referer);
    }

    function test_pixiv2()
    {
        // Manga (medium)

        $url = 'http://www.pixiv.net/member_illust.php?mode=medium&illust_id=51691357';
        $title = "「Manga Example」/「imagelnk」の漫画 [pixiv]";
        $imageurls = array(
            'https://i.pximg.net/img-original/img/2015/07/30/22/18/43/51691357_p0.jpg',
        );
        $referer = 'https://www.pixiv.net/member_illust.php?mode=manga_big&amp;illust_id=51691357&page=0';
        $this->check_response($url, $title, $imageurls, $referer);
    }

    function test_pixiv3()
    {
        // Manga (manga_big)

        $url = 'http://www.pixiv.net/member_illust.php?mode=manga_big&illust_id=51691357&page=1';
        $title = "「Manga Example」/「imagelnk」の漫画 [pixiv]";
        $imageurls = array(
            'https://i.pximg.net/img-original/img/2015/07/30/22/18/43/51691357_p1.jpg',
        );
        $referer = 'https://www.pixiv.net/member_illust.php?mode=manga_big&illust_id=51691357&page=1';

        $this->check_response($url, $title, $imageurls, $referer);
    }

    function test_pixiv4()
    {
        // Manga (all)

        $url = 'http://www.pixiv.net/member_illust.php?mode=manga&illust_id=51691357';
        $title = "Manga Example";
        $imageurls = array(
            'https://i.pximg.net/img-master/img/2015/07/30/22/18/43/51691357_p0_master1200.jpg',
            'https://i.pximg.net/img-master/img/2015/07/30/22/18/43/51691357_p1_master1200.jpg',
        );
        $referer = 'https://www.pixiv.net/member_illust.php?mode=manga&illust_id=51691357';

        $this->check_response($url, $title, $imageurls, $referer);
    }

    function test_pixiv5()
    {
        // touch

        $url = 'http://touch.pixiv.net/member_illust.php?mode=medium&illust_id=51691307';
        $title = "「Image Example」/「imagelnk」のイラスト [pixiv]";
        $imageurls = array(
            'https://i.pximg.net/img-original/img/2015/07/30/22/16/27/51691307_p0.jpg',
        );
        $referer = 'https://www.pixiv.net/member_illust.php?mode=medium&illust_id=51691307';
        $this->check_response($url, $title, $imageurls, $referer);
    }

    // ======================================================================
    function test_yaplog1()
    {
        $url = 'http://yaplog.jp/atsukana/image/236/306';
        $title = '自分大好き日記(笑)の画像(2/5) :: 菜っ葉の『菜』！！';
        $imageurls = array(
            'http://img.yaplog.jp/img/07/pc/a/t/s/atsukana/0/306_large.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_youtube1()
    {
        $url = 'https://www.youtube.com/watch?v=Tlmho7SY-ic&feature=player_embedded';
        $title = 'YouTube Turns Five!';
        $imageurls = array(
            'https://i.ytimg.com/vi/Tlmho7SY-ic/maxresdefault.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_photozou1()
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

    function test_photozou2()
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
    function test_impress1()
    {
        $url = 'https://game.watch.impress.co.jp/img/gmw/docs/448/930/html/psn01.jpg.html';
        $title = '[拡大画像] SCEJ、PlayStation NetworkとQriocityのサービスを5月28日から再開。安全管理措置を導入し、ゲームコンテンツの無償提供も';
        $imageurls = array(
            'https://game.watch.impress.co.jp/img/gmw/docs/448/930/psn01.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_impress2()
    {
        $url = 'https://dc.watch.impress.co.jp/img/dcw/docs/422/882/html/009.jpg.html';
        $title = '[画像] 写真で見る写真で見るカシオ「TRYX」（β機）(9/31) - デジカメ Watch Watch';
        $imageurls = array(
            'https://dc.watch.impress.co.jp/img/dcw/docs/422/882/009.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_impress3()
    {
        $url = 'https://k-tai.watch.impress.co.jp/img/ktw/docs/460/236/html/sanzo.jpg.html';
        $title = '[拡大画像]サンヨーホームズ、Android採用のロボットを搭載した住宅(1/2) -  ケータイ Watch';
        $imageurls = array(
            'https://k-tai.watch.impress.co.jp/img/ktw/docs/460/236/sanzo.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_impress4()
    {
        $url = 'https://akiba-pc.watch.impress.co.jp/hotline/20110806/image/kfrety4.html';
        $title = '[拡大画像]お買い得価格情報 - AKIBA PC Hotline!';
        $imageurls = array(
            'https://akiba-pc.watch.impress.co.jp/hotline/20110806/image/kfrety4.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_tumblr1()
    {
        $url = 'http://titlebot.tumblr.com/post/5544499061/%E3%83%8D%E3%82%B3%E3%81%A8%E5%92%8C%E8%A7%A3%E3%81%9B%E3%82%88';
        $title = 'titlebot';
        $imageurls = array(
            'regex:#http://\d+.media.tumblr.com/tumblr_llal1ttZ7W1qfqa6no1_400.jpg#',
        );
        $referer = 'http://titlebot.tumblr.com/post/5544499061/%E3%83%8D%E3%82%B3%E3%81%A8%E5%92%8C%E8%A7%A3%E3%81%9B%E3%82%88';
        $this->check_response($url, $title, $imageurls, $referer);
    }

    function test_tumblr2()
    {
        $url = 'http://maegamipattun.tumblr.com/post/7815975799';
        $title = '前髪ぱっつん専用タンブラー: blacktights:   iro:   candy-injection:   ...';
        $imageurls = array(
            'regex:#http://\d+.media.tumblr.com/tumblr_l1n113Lyub1qaxrtko1_1280.jpg#',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_wikipedia1()
    {
        $url = 'https://en.wikipedia.org/wiki/File:PANSDeinonychus.JPG';
        $title = 'English: Deinonychus antirrhopus skeleton, Philadelphia Academy of Natural Sciences';
        $imageurls = array(
            'https://upload.wikimedia.org/wikipedia/commons/e/e6/PANSDeinonychus.JPG',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_instagram1()
    {
        $url = 'https://www.instagram.com/p/6XQ5nTTdjq/';
        $title = '@imagelnk on Instagram: “pepper”';
        $imageurls = array(
            'regex:#https://.+.cdninstagram.com/t51.2885-15/e35/11356624_508726059287524_1160649839_n.jpg#',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_instagram2()
    {
        $url = 'https://instagram.com/p/6XQ5nTTdjq/';
        $title = '@imagelnk on Instagram: “pepper”';
        $imageurls = array(
            'regex:#https://.+.cdninstagram.com/t51.2885-15/e35/11356624_508726059287524_1160649839_n.jpg#',
        );
        $referer = 'https://www.instagram.com/p/6XQ5nTTdjq/';
        $this->check_response($url, $title, $imageurls, $referer);
    }

    // ======================================================================
    function test_owly1()
    {
        $url = 'http://ow.ly/i/bG2H';
        $title = 'Ow.ly - image uploaded by @jossfat (Joss Fat): ロシア寿命飲酒量曲線.jpg';
        $imageurls = array(
            'http://static.ow.ly/photos/original/bG2H.jpg',
        );
        $referer = 'http://ow.ly/i/bG2H/original';
        $this->check_response($url, $title, $imageurls, $referer);
    }

    // ======================================================================
    function test_natalie1()
    {
        $url = 'http://natalie.mu/comic/gallery/show/news_id/50403/image_id/77977';
        $title = '物語の舞台である、由比ヶ浜へ連れ出してみるのも一興。(C)安部真弘（週刊少 年チャンピオン）／海の家れもん [画像ギャラリー 3/6] - コミックナタリー';
        $imageurls = array(
            'http://cdn2.natalie.mu/media/comic/1105/extra/news_xlarge_ika_roke1.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_natalie2()
    {
        $url = 'http://natalie.mu/music/gallery/show/news_id/50476/image_id/78087';
        $title = '新しくなった怒髪天のロゴ。 [画像ギャラリー 1/2] - 音楽ナタリー';
        $imageurls = array(
            'http://cdn2.natalie.mu/media/1106/0601/extra/news_xlarge_dohatsuten_topB.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_4gamer1()
    {
        $url = 'http://www.4gamer.net/games/044/G004471/20110616072/screenshot.html?num=003';
        $title = '4Gamer.net ― スクリーンショット（「Wizardry Online」のCBT「機能テスト」先行体験プレイレポートを掲載。実態はやはり高難度……しかし序盤は「ロスト」の心配無用？）';
        $imageurls = array(
            'http://www.4gamer.net/games/044/G004471/20110616072/SS/003.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_4gamer2()
    {
        $url = 'http://www.4gamer.net/games/044/G004471/20110616072/screenshot.html';
        $title = '4Gamer.net ― スクリーンショット（「Wizardry Online」のCBT「機能テスト」先行体験プレイレポートを掲載。実態はやはり高難度……しかし序盤は「ロスト」の心配無用？）';
        $imageurls = array(
            'http://www.4gamer.net/games/044/G004471/20110616072/SS/001.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_famitsu1()
    {
        $url = 'https://www.famitsu.com/news/201106/images/00045516/qSmn53J8Boevo2zZqF3IYq6hCI37GJ7w.html';
        $title = '『侵略！イカ娘』が釣りゲームになって登場！　エビでイカを釣らなイカ？関連スクリーンショット・写真画像';
        $imageurls = array(
            'https://www.famitsu.com/news/201106/images/00045516/qSmn53J8Boevo2zZqF3IYq6hCI37GJ7w.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_famitsu2()
    {
        $url = 'https://www.famitsu.com/news/201106/images/00045487/AGE4AGCB21y4EX6lmIuMOTSYW3Bs4o9Q.html';
        $title = '『ギアーズ オブ ウォー 3』の真髄に迫る、“Horde”いよいよ解禁！【スタジオツアー1】関連スクリーンショット・写真画像';
        $imageurls = array(
            'https://www.famitsu.com/news/201106/images/00045487/AGE4AGCB21y4EX6lmIuMOTSYW3Bs4o9Q.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_zakzak1()
    {
        $url = 'http://www.zakzak.co.jp/gravure/idol/photos/20110627/idl1106271244001-p1.htm';
        $title = '１６歳の森野朝美、スレンダーボディー炸裂にドキッ  - グラビアアイドル - ZAKZAK';
        $imageurls = array(
            'http://www.zakzak.co.jp/gravure/idol/images/20110627/idl1106271244001-p1.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_zakzak2()
    {
        $url = 'http://www.zakzak.co.jp/sports/etc_sports/photos/20150808/spo1508081531009-p1.htm';
        $title = '松山、４９位に後退　池ポチャ２度の大乱調　ブリヂストン招待  - スポーツ - ZAKZAK';
        $imageurls = array(
            'http://www.zakzak.co.jp/sports/etc_sports/images/20150808/spo1508081531009-p1.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_uncyclopedia_ja1()
    {
        $url = 'http://ja.uncyclopedia.info/wiki/%E3%83%95%E3%82%A1%E3%82%A4%E3%83%AB:CaesiumSan_01.jpg';
        $title = 'ファイル:CaesiumSan 01.jpg - アンサイクロペディア';
        $imageurls = array(
            'http://images.uncyc.org/ja/2/25/CaesiumSan_01.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_uncyclopedia_ja2()
    {
        $url = 'http://ja.uncyclopedia.info/wiki/%E3%83%95%E3%82%A1%E3%82%A4%E3%83%AB:Sigeru.jpg';
        $title = 'ファイル:Sigeru.jpg - アンサイクロペディア';
        $imageurls = array(
            'http://images.uncyc.org/ja/1/11/Sigeru.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_uncyclopedia_ja3()
    {
        $url = 'http://ansaikuropedia.org/wiki/%E3%83%95%E3%82%A1%E3%82%A4%E3%83%AB:GRP_0258.JPG';
        $title = 'ファイル:GRP 0258.JPG - アンサイクロペディア';
        $imageurls = array(
            'http://images.uncyc.org/ja/f/f4/GRP_0258.JPG',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_news_livedoor_com1()
    {
        $url = 'http://news.livedoor.com/article/image_detail/5786423/?img_id=2118390';
        $title = '【画像】【こんにちは！ナマな人々】露出度高めなコスプレ娘・知羽音さん 1/2 - ライブドアニュース';
        $imageurls = array(
            'http://image.news.livedoor.com/newsimage/c/0/c08fd40e8bba4eee8ed91b72707e0378.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_news_livedoor_com2()
    {
        $url = 'http://news.livedoor.com/article/image_detail/5460430/?img_id=1848550';
        $title = '【画像】美少女時計に人気モデル 前田希美 黒田瑞貴 志田友美が登場 ウェブ版もスタート 17/18 - ライブドアニュース';
        $imageurls = array(
            'http://image.news.livedoor.com/newsimage/0/1/01e1a_756_b0853.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_twitter1()
    {
        $url = 'https://twitter.com/tekezo/status/474587873925017600';
        $title = 'twitter: Takayama Fumihiko: KeyRemap4MacBook v9.99.11 or later allows you to change keys only when you are editing text (or not). http://t.co/WnuoqQxDRW';
        $imageurls = array(
            'http://pbs.twimg.com/media/BpYTJ-iIcAARRVg.png:large',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_twitter2()
    {
        $url = 'https://mobile.twitter.com/tekezo/status/474587873925017600';
        $title = 'twitter: Takayama Fumihiko: KeyRemap4MacBook v9.99.11 or later allows you to change keys only when you are editing text (or not). http://t.co/WnuoqQxDRW';
        $imageurls = array(
            'http://pbs.twimg.com/media/BpYTJ-iIcAARRVg.png:large',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_itunes1()
    {
        $url = 'https://itunes.apple.com/jp/album/muscle-march-original-soundtrack/id455935658?l=en';
        $title = 'Muscle March Original Soundtrack by Namco Sounds on iTunes';
        $imageurls = array(
            'regex:#https://is.-ssl.mzstatic.com/image/thumb/Music/87/7a/56/mzi.vzoqpscv.jpg/1200x630bb.jpg#',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_cnet_jp1()
    {
        $url = 'https://japan.cnet.com/article/35008398/14/';
        $title = 'NEC PCができるまで--山形の米沢事業所を訪問 - (page 14) - CNET Japan: 　シートを重ねるだけでキートップの相違を検出できる「発見くん」。これは数字の「0」とアルファベットの「O」が誤って取り付けられた例だ。工場には、トヨタ生産方式で知られる「ニンベンの付いた自働化」があらゆるところに取り入れられている。';
        $imageurls = array(
            'https://japan.cnet.com/storage/2011/09/30/9d06e8a0497f76a9d42bdbce397e2796/110930necr9138308.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_cnet_jp2()
    {
        $url = 'https://japan.cnet.com/image/l/storage/35004965/storage/2011/07/07/6158a520ae3ce67be4959c9b8cf62e72/20110707_casio_06.jpg';
        $title = 'CNET Japan: レンズ部を中心にフレームは360度回転し、モニタも270度回転する';
        $imageurls = array(
            'https://japan.cnet.com/storage/2011/07/07/6158a520ae3ce67be4959c9b8cf62e72/20110707_casio_06.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_cnet_jp3()
    {
        $url = 'https://japan.cnet.com/digital/camera/35004965/';
        $expect = null;
        $actual = ImageLnk::getImageInfo($url);
        $this->assertSame($expect, $actual);
    }

    // ======================================================================
    function test_cookpad1()
    {
        $url = 'https://cookpad.com/recipe/720203';
        $title = '大根とツナとホタテのサラダ♪ by ともにゃんママ [クックパッド] 簡単おいしいみんなのレシピが256万品';
        $imageurls = array(
            'https://img.cpcdn.com/recipes/720203/280/24ece10f66b104ef0562b0b2f477d49f.jpg?u=887658&p=1232792798',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_mynavi1()
    {
        $url = 'https://news.mynavi.jp/photo/article/20111108-a008/images/005l.jpg';
        $title = '拡大画像 005 | サイボウズ、クラウド基盤「cybozu.com」の運用を開始しPaaSを提供 | マイナビニュース';
        $imageurls = array(
            'https://news.mynavi.jp/article/20111108-a008/images/005l.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_mynavi2()
    {
        $url = 'https://news.mynavi.jp/photo/article/20110307-appinventor/images/006l.jpg';
        $title = '拡大画像 006l | 経験ゼロでも大丈夫!? App Inventorで始めるAndroidアプリ開発 (1) まずは稼働環境を整備 | マイナビニュース';
        $imageurls = array(
            'https://news.mynavi.jp/article/20110307-appinventor/images/006l.jpg',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_stocks_finance_yahoo_co_jp()
    {
        $url = 'https://stocks.finance.yahoo.co.jp/stocks/detail/?code=3656.T&d=1m';
        $title = 'ＫＬａｂ(株)【3656】：株式/株価 - Yahoo!ファイナンス';
        $imageurls = array(
            'https://chart.yahoo.co.jp/?code=3656.T&tm=1m&size=e',
        );
        $this->check_response($url, $title, $imageurls);
    }

    // ======================================================================
    function test_sankakucomplex1()
    {
        $url = 'https://chan.sankakucomplex.com/post/show/5949839/';
        $title = 'Post 5949839';
        $imageurls = array(
            'regex:#https://cs\.sankakucomplex\.com/data/2e/54/2e540481ae41d3b9d652f1ac92a82b5c\.png\?.*#',
        );
        $this->check_response($url, $title, $imageurls);
    }

    function test_sankakucomplex2()
    {
        $url = 'https://chan.sankakucomplex.com/ja/post/show/6135230';
        $title = 'Post 6135230';
        $imageurls = array(
            'regex:#https://c\.sankakucomplex\.com/sample/7b/44/sample-7b44e9025158c4e7fc0ca1cbaafa3e9d\.jpg\?.*#',
        );
        $this->check_response($url, $title, $imageurls);
    }
}
