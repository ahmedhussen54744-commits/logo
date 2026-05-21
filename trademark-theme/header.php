<?php if (!defined('ABSPATH')) exit; ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="পেটেন্ট, ডিজাইন ও ট্রেডমার্কস অধিদপ্তর - Department of Patents, Designs and Trademarks, Bangladesh">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Top Bar -->
<div class="top-bar">
    <div class="container">
        <div class="top-bar-left">
            <span><i class="fas fa-calendar-alt"></i> <?php echo date_i18n('d F Y, l'); ?></span>
            <span><i class="fas fa-phone"></i> +৮৮-০২-৫৫০০০০০০</span>
        </div>
        <div class="top-bar-right">
            <button class="lang-btn active">বাংলা</button>
            <button class="lang-btn">English</button>
            <a href="#" title="স্ক্রিন রিডার"><i class="fas fa-universal-access"></i></a>
        </div>
    </div>
</div>

<!-- Header -->
<header class="site-header">
    <div class="container">
        <div class="header-inner">
            <div class="logo-area">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f9/Coat_of_arms_of_Bangladesh.svg/120px-Coat_of_arms_of_Bangladesh.svg.png" alt="বাংলাদেশ সরকার">
                <div class="site-identity">
                    <span class="govt-title">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</span>
                    <h1 class="site-title">পেটেন্ট, ডিজাইন ও ট্রেডমার্কস অধিদপ্তর</h1>
                    <span class="ministry">শিল্প মন্ত্রণালয়</span>
                </div>
            </div>
            <div class="header-search">
                <input type="text" placeholder="অনুসন্ধান করুন...">
                <button><i class="fas fa-search"></i></button>
            </div>
        </div>
    </div>
</header>

<!-- Navigation -->
<nav class="main-nav">
    <div class="container">
        <button class="mobile-toggle"><i class="fas fa-bars"></i></button>
        <ul class="nav-list">
            <li><a href="<?php echo home_url(); ?>" class="active"><i class="fas fa-home"></i> হোম</a></li>
            <li>
                <a href="#">আমাদের সম্পর্কে <i class="fas fa-caret-down"></i></a>
                <ul class="sub-menu">
                    <li><a href="#">প্রতিষ্ঠান পরিচিতি</a></li>
                    <li><a href="#">ইতিহাস ও কার্যক্রম</a></li>
                    <li><a href="#">সাংগঠনিক কাঠামো</a></li>
                    <li><a href="#">কর্মকর্তাবৃন্দ</a></li>
                    <li><a href="#">কর্মচারীবৃন্দ</a></li>
                    <li><a href="#">অর্গানোগ্রাম</a></li>
                    <li><a href="#">মিশন ও ভিশন</a></li>
                </ul>
            </li>
            <li>
                <a href="#">সেবাসমূহ <i class="fas fa-caret-down"></i></a>
                <ul class="sub-menu">
                    <li><a href="#">পেটেন্ট নিবন্ধন</a></li>
                    <li><a href="#">ডিজাইন নিবন্ধন</a></li>
                    <li><a href="#">ট্রেডমার্ক নিবন্ধন</a></li>
                    <li><a href="#">জিআই নিবন্ধন</a></li>
                    <li><a href="#">অনলাইন আবেদন</a></li>
                    <li><a href="#">ফি তালিকা</a></li>
                    <li><a href="#">নাগরিক সেবা</a></li>
                    <li><a href="#">সেবা প্রদান প্রতিশ্রুতি</a></li>
                </ul>
            </li>
            <li>
                <a href="#">আইন ও বিধি <i class="fas fa-caret-down"></i></a>
                <ul class="sub-menu">
                    <li><a href="#">পেটেন্ট ও ডিজাইন আইন, ১৯১১</a></li>
                    <li><a href="#">ট্রেডমার্কস আইন, ২০০৯</a></li>
                    <li><a href="#">ভৌগোলিক নির্দেশক পণ্য আইন</a></li>
                    <li><a href="#">বিধিমালা</a></li>
                    <li><a href="#">পরিপত্র/নির্দেশিকা</a></li>
                    <li><a href="#">গেজেট</a></li>
                </ul>
            </li>
            <li>
                <a href="#">প্রকাশনা <i class="fas fa-caret-down"></i></a>
                <ul class="sub-menu">
                    <li><a href="#">ট্রেডমার্ক জার্নাল</a></li>
                    <li><a href="#">বার্ষিক প্রতিবেদন</a></li>
                    <li><a href="#">প্রেস রিলিজ</a></li>
                    <li><a href="#">নিউজলেটার</a></li>
                    <li><a href="#">গবেষণা পত্র</a></li>
                </ul>
            </li>
            <li>
                <a href="#">ফরম ও ডাউনলোড <i class="fas fa-caret-down"></i></a>
                <ul class="sub-menu">
                    <li><a href="#">পেটেন্ট ফরম</a></li>
                    <li><a href="#">ডিজাইন ফরম</a></li>
                    <li><a href="#">ট্রেডমার্ক ফরম</a></li>
                    <li><a href="#">জিআই ফরম</a></li>
                    <li><a href="#">অন্যান্য ফরম</a></li>
                    <li><a href="#">ম্যানুয়াল</a></li>
                </ul>
            </li>
            <li>
                <a href="#">তথ্য ভান্ডার <i class="fas fa-caret-down"></i></a>
                <ul class="sub-menu">
                    <li><a href="#">ট্রেডমার্ক ডাটাবেস</a></li>
                    <li><a href="#">পেটেন্ট ডাটাবেস</a></li>
                    <li><a href="#">ডিজাইন ডাটাবেস</a></li>
                    <li><a href="#">পরিসংখ্যান</a></li>
                    <li><a href="#">WIPO ডাটা</a></li>
                </ul>
            </li>
            <li>
                <a href="#">ই-সেবা <i class="fas fa-caret-down"></i></a>
                <ul class="sub-menu">
                    <li><a href="#">অনলাইন আবেদন</a></li>
                    <li><a href="#">আবেদনের অবস্থা</a></li>
                    <li><a href="#">সার্টিফিকেট যাচাই</a></li>
                    <li><a href="#">ই-পেমেন্ট</a></li>
                    <li><a href="#">অভিযোগ দাখিল</a></li>
                </ul>
            </li>
            <li>
                <a href="#">গ্যালারি <i class="fas fa-caret-down"></i></a>
                <ul class="sub-menu">
                    <li><a href="#">ফটো গ্যালারি</a></li>
                    <li><a href="#">ভিডিও গ্যালারি</a></li>
                </ul>
            </li>
            <li><a href="#">নোটিশ বোর্ড</a></li>
            <li><a href="#">যোগাযোগ</a></li>
        </ul>
    </div>
</nav>
