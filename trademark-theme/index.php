<?php
/**
 * Main Template - DPDT Homepage
 * Since: 2009
 */
if (!defined('ABSPATH')) exit;
get_header();
?>

<!-- Hero Slider -->
<section class="hero-section">
    <div class="hero-slide active" style="background: linear-gradient(135deg, #006a4e 0%, #004d3a 100%);">
        <div class="hero-slide-content">
            <h2>পেটেন্ট, ডিজাইন ও ট্রেডমার্কস অধিদপ্তরে স্বাগতম</h2>
            <p>মেধা সম্পদ সুরক্ষায় আমরা প্রতিশ্রুতিবদ্ধ</p>
            <a href="#" class="hero-btn">আরও জানুন</a>
        </div>
    </div>
    <div class="hero-slide" style="background: linear-gradient(135deg, #1a5276 0%, #154360 100%);">
        <div class="hero-slide-content">
            <h2>অনলাইনে ট্রেডমার্ক সার্টিফিকেট আবেদন করুন</h2>
            <p>দ্রুত ও নিরাপদে ট্রেডমার্ক নিবন্ধন সেবা পান</p>
            <a href="<?php echo home_url('/apply'); ?>" class="hero-btn">এখনই আবেদন করুন</a>
        </div>
    </div>
    <div class="hero-slide" style="background: linear-gradient(135deg, #7d3c98 0%, #5b2c6f 100%);">
        <div class="hero-slide-content">
            <h2>সার্টিফিকেট যাচাই করুন</h2>
            <p>QR কোড বা রেজিস্ট্রেশন নম্বর দিয়ে যাচাই করুন</p>
            <a href="<?php echo home_url('/verify'); ?>" class="hero-btn">যাচাই করুন</a>
        </div>
    </div>
    <div class="hero-slide" style="background: linear-gradient(135deg, #d35400 0%, #c0392b 100%);">
        <div class="hero-slide-content">
            <h2>পেটেন্ট নিবন্ধন</h2>
            <p>আপনার উদ্ভাবনকে আইনি সুরক্ষা দিন</p>
            <a href="#" class="hero-btn">বিস্তারিত জানুন</a>
        </div>
    </div>
    <button class="slider-nav prev"><i class="fas fa-chevron-left"></i></button>
    <button class="slider-nav next"><i class="fas fa-chevron-right"></i></button>
    <div class="slider-indicators"></div>
</section>

<!-- Quick Services -->
<section class="services-section">
    <div class="container">
        <div class="section-heading">
            <h2>আমাদের সেবাসমূহ</h2>
            <p>দ্রুত এবং দক্ষ সেবা প্রদানে আমরা প্রতিশ্রুতিবদ্ধ</p>
            <div class="underline"></div>
        </div>
        <div class="services-grid">
            <a href="#" class="service-item">
                <div class="icon"><i class="fas fa-lightbulb"></i></div>
                <h3>পেটেন্ট নিবন্ধন</h3>
                <p>উদ্ভাবন সুরক্ষা</p>
            </a>
            <a href="#" class="service-item">
                <div class="icon"><i class="fas fa-pencil-ruler"></i></div>
                <h3>ডিজাইন নিবন্ধন</h3>
                <p>শিল্প-নকশা সুরক্ষা</p>
            </a>
            <a href="<?php echo home_url('/apply'); ?>" class="service-item">
                <div class="icon"><i class="fas fa-trademark"></i></div>
                <h3>ট্রেডমার্ক নিবন্ধন</h3>
                <p>ব্র্যান্ড সুরক্ষা</p>
            </a>
            <a href="#" class="service-item">
                <div class="icon"><i class="fas fa-globe-asia"></i></div>
                <h3>জিআই নিবন্ধন</h3>
                <p>ভৌগোলিক নির্দেশক</p>
            </a>
            <a href="#" class="service-item">
                <div class="icon"><i class="fas fa-laptop-code"></i></div>
                <h3>অনলাইন আবেদন</h3>
                <p>ই-ফাইলিং সিস্টেম</p>
            </a>
            <a href="#" class="service-item">
                <div class="icon"><i class="fas fa-search"></i></div>
                <h3>ট্রেডমার্ক অনুসন্ধান</h3>
                <p>ডাটাবেসে খুঁজুন</p>
            </a>
            <a href="<?php echo home_url('/verify'); ?>" class="service-item">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <h3>সার্টিফিকেট যাচাই</h3>
                <p>অনলাইন ভেরিফিকেশন</p>
            </a>
            <a href="#" class="service-item">
                <div class="icon"><i class="fas fa-book-open"></i></div>
                <h3>ট্রেডমার্ক জার্নাল</h3>
                <p>সাম্প্রতিক প্রকাশনা</p>
            </a>
            <a href="#" class="service-item">
                <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                <h3>ফি তালিকা</h3>
                <p>সেবামূল্য জানুন</p>
            </a>
            <a href="#" class="service-item">
                <div class="icon"><i class="fas fa-file-download"></i></div>
                <h3>ফরম ডাউনলোড</h3>
                <p>প্রয়োজনীয় ফরম</p>
            </a>
            <a href="#" class="service-item">
                <div class="icon"><i class="fas fa-headset"></i></div>
                <h3>অভিযোগ দাখিল</h3>
                <p>GRS সিস্টেম</p>
            </a>
            <a href="#" class="service-item">
                <div class="icon"><i class="fas fa-university"></i></div>
                <h3>প্রশিক্ষণ</h3>
                <p>আইপি সচেতনতা</p>
            </a>
        </div>
    </div>
</section>

<!-- Content Section: Notice + Activities -->
<section class="content-section">
    <div class="container">
        <div class="content-grid">
            <div class="content-box">
                <div class="content-box-header">
                    <h3><i class="fas fa-bullhorn"></i> নোটিশ বোর্ড</h3>
                    <a href="#" class="btn-sm">সব দেখুন</a>
                </div>
                <div class="content-box-body">
                    <div class="notice-item">
                        <span class="notice-date">২০/০৫/২০২৬</span>
                        <a href="#">ট্রেডমার্ক নবায়ন সংক্রান্ত জরুরি বিজ্ঞপ্তি</a>
                        <span class="badge-new">নতুন</span>
                    </div>
                    <div class="notice-item">
                        <span class="notice-date">১৫/০৫/২০২৬</span>
                        <a href="#">অনলাইন সার্টিফিকেট যাচাই সিস্টেম চালু</a>
                        <span class="badge-new">নতুন</span>
                    </div>
                    <div class="notice-item">
                        <span class="notice-date">১০/০৫/২০২৬</span>
                        <a href="#">পেটেন্ট আবেদনের নতুন ফি কাঠামো কার্যকর</a>
                    </div>
                    <div class="notice-item">
                        <span class="notice-date">০৫/০৫/২০২৬</span>
                        <a href="#">বার্ষিক প্রতিবেদন ২০২৫-২৬ প্রকাশিত</a>
                    </div>
                    <div class="notice-item">
                        <span class="notice-date">০১/০৫/২০২৬</span>
                        <a href="#">মেধাসম্পদ দিবস উদযাপন সেমিনার আয়োজন</a>
                    </div>
                    <div class="notice-item">
                        <span class="notice-date">২৬/০৪/২০২৬</span>
                        <a href="#">নিয়োগ বিজ্ঞপ্তি - পরীক্ষক পদে নিয়োগ</a>
                    </div>
                </div>
            </div>
            <div class="content-box">
                <div class="content-box-header">
                    <h3><i class="fas fa-newspaper"></i> সাম্প্রতিক কার্যক্রম</h3>
                    <a href="#" class="btn-sm">সব দেখুন</a>
                </div>
                <div class="content-box-body">
                    <div class="notice-item">
                        <span class="notice-date">২৬/০৪/২০২৬</span>
                        <a href="#">বিশ্ব মেধাসম্পদ দিবস ২০২৬ উদযাপন</a>
                    </div>
                    <div class="notice-item">
                        <span class="notice-date">১৫/০৩/২০২৬</span>
                        <a href="#">ট্রেডমার্ক ই-ফাইলিং সিস্টেম আপগ্রেড</a>
                    </div>
                    <div class="notice-item">
                        <span class="notice-date">০১/০৩/২০২৬</span>
                        <a href="#">WIPO-এর সাথে যৌথ কর্মশালা অনুষ্ঠিত</a>
                    </div>
                    <div class="notice-item">
                        <span class="notice-date">২০/০২/২০২৬</span>
                        <a href="#">মহাপরিচালকের কার্যালয়ে সভা অনুষ্ঠিত</a>
                    </div>
                    <div class="notice-item">
                        <span class="notice-date">১০/০২/২০২৬</span>
                        <a href="#">আইপি সচেতনতা বৃদ্ধি প্রোগ্রাম সফল</a>
                    </div>
                    <div class="notice-item">
                        <span class="notice-date">০১/০১/২০২৬</span>
                        <a href="#">নতুন বছরে অনলাইন সেবা সম্প্রসারণ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics -->
<section class="stats-section">
    <div class="container">
        <div class="section-heading">
            <h2 style="color:#fff">সেবা পরিসংখ্যান</h2>
            <div class="underline" style="background:#fff"></div>
        </div>
        <div class="stats-grid">
            <div class="stat-box">
                <i class="fas fa-file-signature"></i>
                <div class="number" data-target="52430">০</div>
                <div class="label">মোট ট্রেডমার্ক নিবন্ধন</div>
            </div>
            <div class="stat-box">
                <i class="fas fa-lightbulb"></i>
                <div class="number" data-target="4180">০</div>
                <div class="label">মোট পেটেন্ট নিবন্ধন</div>
            </div>
            <div class="stat-box">
                <i class="fas fa-palette"></i>
                <div class="number" data-target="11250">০</div>
                <div class="label">মোট ডিজাইন নিবন্ধন</div>
            </div>
            <div class="stat-box">
                <i class="fas fa-hourglass-half"></i>
                <div class="number" data-target="2340">০</div>
                <div class="label">চলমান আবেদন</div>
            </div>
        </div>
    </div>
</section>

<!-- Important Links -->
<section class="links-section">
    <div class="container">
        <div class="section-heading">
            <h2>গুরুত্বপূর্ণ লিংক</h2>
            <div class="underline"></div>
        </div>
        <div class="links-grid">
            <a href="https://www.bangladesh.gov.bd" target="_blank" class="link-item">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f9/Coat_of_arms_of_Bangladesh.svg/60px-Coat_of_arms_of_Bangladesh.svg.png" alt="">
                <span>জাতীয় তথ্য বাতায়ন</span>
            </a>
            <a href="https://www.moind.gov.bd" target="_blank" class="link-item">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f9/Coat_of_arms_of_Bangladesh.svg/60px-Coat_of_arms_of_Bangladesh.svg.png" alt="">
                <span>শিল্প মন্ত্রণালয়</span>
            </a>
            <a href="https://www.wipo.int" target="_blank" class="link-item">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/58/WIPO_logo.svg/60px-WIPO_logo.svg.png" alt="">
                <span>WIPO</span>
            </a>
            <a href="https://www.pmo.gov.bd" target="_blank" class="link-item">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f9/Coat_of_arms_of_Bangladesh.svg/60px-Coat_of_arms_of_Bangladesh.svg.png" alt="">
                <span>প্রধানমন্ত্রীর কার্যালয়</span>
            </a>
            <a href="https://www.mygov.bd" target="_blank" class="link-item">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f9/Coat_of_arms_of_Bangladesh.svg/60px-Coat_of_arms_of_Bangladesh.svg.png" alt="">
                <span>মাইগভ</span>
            </a>
            <a href="https://a2i.gov.bd" target="_blank" class="link-item">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f9/Coat_of_arms_of_Bangladesh.svg/60px-Coat_of_arms_of_Bangladesh.svg.png" alt="">
                <span>a2i প্রোগ্রাম</span>
            </a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
