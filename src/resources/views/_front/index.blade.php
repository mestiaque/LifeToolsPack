<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1" />
  <title>M. Estiaque Ahmed Khan | Software Engineer & Developer | Portfolio</title>
  <meta name="description" content="Professional portfolio of M. Estiaque Ahmed Khan, a skilled Software Engineer specializing in PHP, Laravel, and full-stack web development based in Dhaka, Bangladesh.">
  <meta name="keywords" content="M. Estiaque Ahmed Khan, Software Engineer, Laravel Developer, Web Developer, PHP Developer, Full-Stack Developer, Bangladesh Developer, Portfolio">
  <meta name="author" content="M. Estiaque Ahmed Khan">
  <meta name="robots" content="index, follow">
  <meta property="og:title" content="M. Estiaque Ahmed Khan | Software Engineer & Developer">
  <meta property="og:description" content="Professional portfolio showcasing the work of M. Estiaque Ahmed Khan, a Software Engineer specializing in PHP, Laravel, and full-stack web development.">
  <meta property="og:image" content="{{ asset('assets/img/favicon/Encodex.ico') }}">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:type" content="website">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <link rel="canonical" href="{{ url()->current() }}">
  <!-- Favicons -->
  <link href="{{ asset('assets/img/favicon/Encodex.ico') }}" rel="icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700|Raleway:400,500,700|Poppins:400,500,600,700" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('front/vendor/aos/aos.css')}}" rel="stylesheet">
  <link href="{{ asset('front/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{ asset('front/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
  <link href="{{ asset('front/vendor/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
  <link href="{{ asset('front/vendor/glightbox/css/glightbox.min.css')}}" rel="stylesheet">
  <link href="{{ asset('front/vendor/swiper/swiper-bundle.min.css')}}" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="{{asset('front/css/style.css')}}" rel="stylesheet">
  <link href="{{asset('front/css/custom.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="{{asset('front/css/icon.css')}}">
  <style>
    :root {
      /* --banner-img: url('{{ asset('assets/img/' . ($settings->banner_img ?? 'cover.jpeg')) }}'); */
        --primary-color: #0078d4;
        --secondary-color: #00b1f5;
        --accent-color: #5f2eea;
        --text-color: #2d3748;
        --light-bg: #f8f9fb;
        --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        --hover-shadow: 0 8px 28px rgba(0, 120, 212, 0.15);
    }
    body {
      font-family: 'Poppins', 'Raleway', 'Open Sans', sans-serif;
      color: #232323;
      background: #f8f9fa;
    }
    .section-title h2 {
      font-weight: 700;
      letter-spacing: 1px;
      color: #00b1f5;
    }
    .resume-item h4 {
      color: #222;
      font-size: 1.1rem;
      font-weight: 600;
    }
    .resume .table td, .resume .table th {
      border: none;
      padding: 0.3rem 0.7rem;
      font-size: 0.97rem;
    }
    .project-card,
    .equal-card,
    .equal-cardx,
    .skill-card,
    .about-card,
    .contact-form,
    .contact .info .icon-box {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px #e0e0e0;
      margin-bottom: 1.2rem;
      padding: 1rem 0.7rem 0.7rem 0.7rem;
      transition: box-shadow 0.2s, transform 0.2s;
      /* height: 100%; */
    }
    .project-card:hover,
    .equal-card:hover,
    .equal-cardx:hover,
    .skill-card:hover,
    .about-card:hover,
    .contact-form:hover,
    .contact .info .icon-box:hover {
      box-shadow: 0 6px 24px #b0d8f7;
      transform: translateY(-2px);
    }
    .equal-card {
      min-height: 300px;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      align-items: stretch;
    }
    /* Contact Section Enhancements */
    .contact .info .icon-box {
      background: #fff;
      border-radius: 8px;
      padding: 1rem 1.3rem;
      margin-bottom: 1rem;
      box-shadow: 0 2px 8px #d1e8ff;
      display: flex;
      align-items: center;
      font-size: 1.05rem;
    }
    .contact .info .icon-box i {
      color: #00b1f5;
      margin-right: 1rem;
      font-size: 1.6rem;
    }
    .contact-form {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px #d1e8ff;
      padding: 2rem 1.5rem;
    }
    .contact-form .form-control {
      border-radius: 5px;
      margin-bottom: 1rem;
    }
    .btn-primary {
      background: #00b1f5;
      border: none;
      padding: 0.7rem 2.1rem;
      font-weight: 600;
      border-radius: 7px;
      transition: background 0.2s;
    }
    .btn-primary:hover {
      background: #0091c7;
    }
    /* Skills Progress Bars */
    .skills .progress-bar {
      background: linear-gradient(90deg, #00b1f5 70%, #eaf6fb 100%);
      border-radius: 6px;
      height: 10px;
    }
    .skills .progress {
      margin-bottom: 1rem;
    }
    .skills .skill {
      font-size: 1.05rem;
      font-weight: 500;
      margin-bottom: 0.25rem;
    }
    #skills{
        padding-top: 120px !important;
    }
    /* About Section Enhancements */
    .about-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 12px #e3f2fd;
      padding: 2.2rem 2rem 1.5rem 2rem;
      border: 1px solid #e3f2fd;
      /* margin-bottom: 2rem; */    }
    .about .section-title h2 {
      font-size: 2rem;
      font-weight: 700;
      color: #00b1f5;
      margin-bottom: 1.2rem;
      letter-spacing: 1px;
    }
    .about-profile-img {
        width: 310px;
        height: auto;
        border-radius: 8px;
        border: 4px solid #00b1f5;
        box-shadow: 0 2px 12px #e3f2fd;
        /* margin-bottom: 1.2rem; */
        background: #f6fbff;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
    .about-info-list {
      list-style: none;
      padding-left: 0;
      margin-bottom: 1.1rem;
    }
    .about-info-list li {
      margin-bottom: 0.5rem;
      font-size: 1.05rem;
      display: flex;
      align-items: center;
    }
    .about-info-list i {
      color: #00b1f5;
      margin-right: 0.7rem;
      font-size: 1.2rem;
    }
    .about-designation {
      font-size: 1.2rem;
      font-weight: 600;
      color: #0091c7;
      margin-bottom: 1rem;
    }
    .about-desc {
      font-size: 1.07rem;
      color: #444;
      margin-bottom: 1.2rem;
      line-height: 1.7;
    }
    .about-extra {
      font-size: 1rem;
      color: #666;
      margin-top: 0.7rem;
      margin-bottom: 0.2rem;
    }
    @media (max-width: 768px) {
      .about-card { padding: 1.2rem 0.7rem; }
      /* .about-profile-img { width: 120px; height: 120px; } */
    }
    /* Responsive Tweak */
    @media (max-width: 768px) {
      .project-card { padding: 0.8rem; }
    }
    .edu-title {
      font-size: 1.45rem;
      font-weight: 600;
      color: #00b1f5;
      margin-bottom: 0.5rem;
      margin-top: 0.3rem;
      text-align: center;
    }
    .edu-period {
      font-size: 1rem;
      color: #555;
      margin-bottom: 0.4rem;
      text-align: center;
    }
    .edu-table {
      width: 100%;
      margin-bottom: 0.5rem;
      font-size: 0.98rem;
      background: #f6fbff;
      border-radius: 6px;
      border: 1px solid #e3f2fd;
      text-align: center !important;
    }
    .edu-table td {
      padding: 0.35rem 0.7rem;
      border: none;
    }
    .back-to-top{
        background: lime !important;
    }

    .nav-menu a:hover,
    .nav-menu a.active {
        color: #fff !important;
        background: linear-gradient(to right, var(--primary-color), #0078d4, var(--secondary-color)) !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        transform: translateX(8px);
    }

    .section-title h2::after {
        width: 60px;
        background: linear-gradient(90deg, var(--secondary-color), var(--accent-color));
        left: calc(50% - 30px);
    }

    .back-to-top {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
    }

    #hero h1{
        color:#0f2d4a !important;
    }

    .gradient-text, .designation{
        background: linear-gradient(190deg, var(--secondary-color), var(--accent-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text; /* Firefox support */
        color: transparent;    /* fallback */
    }

    .social-links a {
        display: inline-block;
        margin: 0 8px;
        font-size: 22px;
        transition: transform 0.3s ease, color 0.3s ease;
    }

    /* Twitter */
    .social-links a.twitter {
        color: #1DA1F2 !important; /* Official Twitter Blue */
    }
    .social-links a.twitter:hover {
        color: #0d8ddb !important;
        transform: scale(1.2);
    }

    /* Facebook */
    .social-links a.facebook {
        color: #1877F2 !important; /* Official Facebook Blue */
    }
    .social-links a.facebook:hover {
        color: #145dbf !important;
        transform: scale(1.2);
    }

    /* Instagram */
    .social-links a.instagram {
        color: #E4405F !important; /* Instagram Pink/Red */
    }
    .social-links a.instagram:hover {
        color: #c32aa3 !important;
        transform: scale(1.2);
    }

    /* LinkedIn */
    .social-links a.linkedin {
        color: #0A66C2 !important; /* Official LinkedIn Blue */
    }
    .social-links a.linkedin:hover {
        color: #004182 !important;
        transform: scale(1.2);
    }

    @media (max-width: 768px) {
        #hero {
            background-position: right -55px top!important;
        }
    }

  </style>
  <style>
    /* Success Animation Styles */
    #success-animation {
        text-align: center;
        padding: 20px;
        margin-top: 4rem;
    }

    .thank-you-message {
        color: #00b1f5;
        font-size: 1.5rem;
        margin-top: 15px;
        font-weight: 600;
        animation: fadeInUp 0.8s;
    }

    .follow-up-message {
        color: #555;
        font-size: 1.1rem;
        animation: fadeInUp 1s;
    }

    .checkmark-circle {
        width: 80px;
        height: 80px;
        position: relative;
        display: inline-block;
        vertical-align: top;
        margin-bottom: 10px;
    }

    .checkmark-circle .background {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #00b1f5;
        position: absolute;
    }

    .checkmark-circle .checkmark {
        border-radius: 5px;
    }

    .checkmark-circle .checkmark.draw:after {
        animation-delay: 0.3s;
        animation-duration: 0.8s;
        animation-timing-function: ease;
        animation-name: checkmark;
        transform: scaleX(-1) rotate(135deg);
        animation-fill-mode: forwards;
    }

    .checkmark-circle .checkmark:after {
        opacity: 1;
        height: 40px;
        width: 20px;
        transform-origin: left top;
        border-right: 5px solid #fff;
        border-top: 5px solid #fff;
        content: '';
        left: 20px;
        top: 42px;
        position: absolute;
    }

    @keyframes checkmark {
        0% {
        height: 0;
        width: 0;
        opacity: 1;
        }
        20% {
        height: 0;
        width: 20px;
        opacity: 1;
        }
        40% {
        height: 40px;
        width: 20px;
        opacity: 1;
        }
        100% {
        height: 40px;
        width: 20px;
        opacity: 1;
        }
    }

    @keyframes fadeInUp {
        from {
        opacity: 0;
        transform: translate3d(0, 30px, 0);
        }
        to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
        }
    }
</style>
</head>

<body itemscope itemtype="https://schema.org/Person">

  <!-- ======= Mobile nav toggle button ======= -->
  <i class="bi bi-list mobile-nav-toggle d-lg-none" aria-label="Toggle navigation"></i>

  <!-- ======= Header ======= -->
  <header id="header" class="d-flex flex-column justify-content-center">
    <nav id="navbar" class="navbar nav-menu" aria-label="Main navigation">
      <ul>
        <li><a href="#hero" class="nav-link scrollto active" aria-label="Home section"><i class="bx bx-home" aria-hidden="true"></i> <span>Home</span></a></li>
        <li><a href="#about" class="nav-link scrollto" aria-label="About section"><i class="bx bx-user" aria-hidden="true"></i> <span>About</span></a></li>
        <li><a href="#job" class="nav-link scrollto" aria-label="Experience section"><i class="bx bx-briefcase" aria-hidden="true"></i> <span>Experience</span></a></li>
        <li><a href="#projects" class="nav-link scrollto" aria-label="Projects section"><i class="bx bx-code-block" aria-hidden="true"></i> <span>Projects</span></a></li>
        <li><a href="#resume" class="nav-link scrollto" aria-label="Qualifications section"><i class="bi bi-file-earmark-richtext" aria-hidden="true"></i> <span>Qualifications</span></a></li>
        <li><a href="#contact" class="nav-link scrollto" aria-label="Contact section"><i class="bx bx-envelope" aria-hidden="true"></i> <span>Contact</span></a></li>
      </ul>
    </nav>
  </header>

  <!-- ======= Hero Section ======= -->
  <section id="hero" class="d-flex flex-column justify-content-center" style="background:url('{{ asset('storage/images/banner_image/' . get_setting('banner_image')) }}') top right no-repeat; background-size:cover" aria-label="Hero banner">
    <div class="container" data-aos="zoom-in" data-aos-delay="100">
      <h1 itemprop="name">M. Estiaque Ahmed Khan</h1>
      <p>I'm <span class="typed designation" data-typed-items="{{ get_setting('designation') ?? 'Engineer, Software Developer' }}" itemprop="jobTitle"></span></p>
      <div class="social-links mt-3">
            <a href="{{ get_setting('facebook_link') }}" target="_blank" rel="noopener" class="facebook" title="Connect on Facebook" itemprop="sameAs"><i class="bx bxl-facebook" aria-hidden="true"></i><span class="sr-only"></span></a>
            <a href="{{ get_setting('instagram_link') }}" target="_blank" rel="noopener" class="instagram" title="Follow on Instagram" itemprop="sameAs"><i class="bx bxl-instagram" aria-hidden="true"></i><span class="sr-only"></span></a>
            <a href="{{ get_setting('twitter_link') }}" target="_blank" rel="noopener" class="twitter" title="Follow on Twitter" itemprop="sameAs"><i class="bx bxl-twitter" aria-hidden="true"></i><span class="sr-only"></span></a>
            <a href="{{ get_setting('linkedin_link') }}" target="_blank" rel="noopener" class="linkedin" title="Connect on LinkedIn" itemprop="sameAs"><i class="bx bxl-linkedin" aria-hidden="true"></i><span class="sr-only"></span></a>
      </div>
    </div>
  </section>

  <main id="main">

    <!-- ======= About Section ======= -->
    <section id="about" class="about section-bg" aria-labelledby="about-heading">
      <div class="container" data-aos="fade-up">
        <div class="about-card">
          <div class="section-title text-center">
            <h2 id="about-heading">About Me</h2>
          </div>
          <div class="row align-items-center">
            <div class="col-lg-4 text-center mb-3 mb-lg-0">
              <img loading="lazy" src="{{ asset('storage/images/profile_image/' . get_setting('profile_image')) }}" class="about-profile-img" alt="M. Estiaque Ahmed Khan - Professional Portrait" itemprop="image">
            </div>
            <div class="col-lg-8 pt-4 pt-lg-0 content">
              <div class="about-designation" itemprop="jobTitle">{{ $settings->designation ?? 'Software Engineer' }}</div>
              <div class="row">
                <div class="col-md-6">
                  <ul class="about-info-list">
                    <li><i class="bi bi-calendar-event" aria-hidden="true"></i> <strong>DoB:</strong> <span itemprop="birthDate">15 December 1998</span></li>
                    <li><i class="bi bi-phone" aria-hidden="true"></i> <strong>Phone:</strong> <span itemprop="telephone">{{get_setting('mobile') ?? '09696009656'}}</span></li>
                    <li><i class="bi bi-envelope" aria-hidden="true"></i> <strong>Email:</strong> <span itemprop="email">{{ get_setting('email') ?? 'info@mestiaque.com' }}</span></li>
                    <li><i class="bi bi-geo-alt" aria-hidden="true"></i> <strong>City:</strong> <span itemprop="homeLocation">Dhaka, Bangladesh</span></li>
                  </ul>
                </div>
                <div class="col-md-6">
                  @php
                    $dob = \Carbon\Carbon::parse('1998-12-15');
                    $age = $dob->age;
                  @endphp
                  <ul class="about-info-list">
                    {{-- <li><i class="bi bi-person"></i> <strong>Age:</strong> <span>{{ $age ?? 'N/A' }}</span></li> --}}
                    <li><i class="bi bi-mortarboard" aria-hidden="true"></i> <strong>Degree:</strong> <span>Masters</span></li>
                    <li><i class="bi bi-flag" aria-hidden="true"></i> <strong>Nationality:</strong> <span itemprop="nationality">Bangladeshi</span></li>
                    <li><i class="bi bi-heart" aria-hidden="true"></i> <strong>Religion:</strong> <span>Islam</span></li>
                    <li><i class="bi bi-droplet" aria-hidden="true"></i> <strong>Blood Group:</strong> <span class="text-danger">O+</span></li>
                  </ul>
                </div>
              </div>
              <div class="about-desc" itemprop="description">
                Passionate Software Engineer with expertise in full-stack web development, specializing in PHP (Laravel), JavaScript, and Bootstrap. Dedicated to building robust, scalable solutions. Always eager to learn new technologies and contribute to innovative projects.
              </div>
              <div class="about-extra">
                Driven by the desire to solve real-world problems, I constantly seek opportunities to grow both technically and professionally. My journey spans multiple projects, from web applications to backend APIs.
              </div>
            </div>
          </div>
        </div>
        <!-- Skills Section -->
        <div id="skills" class="skills section-bg" aria-labelledby="skills-heading">
          <div class="container" data-aos="fade-up">
            <div class="section-title">
              <h2 id="skills-heading">Professional Skills</h2>
            </div>
            <div class="row">
              <div class="col-lg-6 mb-4">
                <div class="project-card skill-card">
                  <h3 class="mb-3" style="color:#0091c7;font-weight:600;">Frontend Skills</h3>
                  <div class="progress mb-3"><span class="skill">HTML</span>
                    <div class="progress-bar-wrap">
                      <div class="progress-bar" role="progressbar" style="width:85%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="progress mb-3"><span class="skill">CSS</span>
                    <div class="progress-bar-wrap">
                      <div class="progress-bar" role="progressbar" style="width:75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="progress mb-3"><span class="skill">JavaScript</span>
                    <div class="progress-bar-wrap">
                      <div class="progress-bar" role="progressbar" style="width:65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="progress mb-3"><span class="skill">Bootstrap</span>
                    <div class="progress-bar-wrap">
                      <div class="progress-bar" role="progressbar" style="width:85%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 mb-4">
                <div class="project-card skill-card">
                  <h3 class="mb-3" style="color:#0091c7;font-weight:600;">Backend & Others</h3>
                  <div class="progress mb-3"><span class="skill">PHP (Laravel)</span>
                    <div class="progress-bar-wrap">
                      <div class="progress-bar" role="progressbar" style="width:80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="progress mb-3"><span class="skill">Ajax</span>
                    <div class="progress-bar-wrap">
                      <div class="progress-bar" role="progressbar" style="width:60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="progress mb-3"><span class="skill">MySQL</span>
                    <div class="progress-bar-wrap">
                      <div class="progress-bar" role="progressbar" style="width:70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                  <div class="progress mb-3"><span class="skill">Git</span>
                    <div class="progress-bar-wrap">
                      <div class="progress-bar" role="progressbar" style="width:65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ======= Job/Experience Section ======= -->
    <section id="job" class="resume" aria-labelledby="experience-heading">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2 id="experience-heading">Professional Experience</h2>
        </div>
        <div class="row">
          @php
            $jobs = [
                [
                    'title' => 'Software Engineer',
                    'period' => 'April 2024 – Present',
                    'company' => 'Isotope IT Ltd., Dhaka',
                    'details' => [
                        'Designing and developing dynamic websites and enterprise management software solutions.',
                        'Building scalable backend systems with Laravel and integrating interactive front-end features.',
                        'Collaborating with clients to gather requirements and delivering tailored software solutions.',
                        'Ensuring application security, performance optimization, and smooth deployment processes.',
                    ]
                ],
                [
                    'title' => 'Software Developer',
                    'period' => 'September 2022 – March 2024',
                    'company' => 'Barcodetech Automation Ltd., Dhaka',
                    'details' => [
                        'Developed management software solutions to streamline business operations.',
                        'Integrated barcode scanning systems with customized software modules.',
                        'Worked closely with clients to understand workflows and implement automation features.',
                        'Maintained existing applications and enhanced features based on user feedback.',
                    ]
                ],
            ];
          @endphp

          @if(count($jobs) % 2 != 0 && count($jobs) > 0)
            @for($i = 0; $i < count($jobs) - 1; $i++)
              <div class="col-lg-6 mb-4">
                <div class="project-card equal-card" itemscope itemtype="https://schema.org/WorkPosition">
                  <h3 itemprop="jobTitle">{{ $jobs[$i]['title'] }}</h3>
                  <h4 itemprop="validFrom">{{ $jobs[$i]['period'] }}</h4>
                  <p><em itemprop="memberOf">{{ $jobs[$i]['company'] }}</em></p>
                  <ul>
                    @foreach($jobs[$i]['details'] as $detail)
                      <li itemprop="description">{{ $detail }}</li>
                    @endforeach
                  </ul>
                </div>
              </div>
            @endfor
            <div class="col-lg-6 mb-4 mx-auto">
              <div class="project-card equal-card" itemscope itemtype="https://schema.org/WorkPosition">
                <h3 itemprop="jobTitle">{{ $jobs[count($jobs) - 1]['title'] }}</h3>
                <h4 itemprop="validFrom">{{ $jobs[count($jobs) - 1]['period'] }}</h4>
                <p><em itemprop="memberOf">{{ $jobs[count($jobs) - 1]['company'] }}</em></p>
                <ul>
                  @foreach($jobs[count($jobs) - 1]['details'] as $detail)
                    <li itemprop="description">{{ $detail }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          @else
            @for($i = 0; $i < count($jobs); $i++)
              <div class="col-lg-6 mb-4">
                <div class="project-card equal-card" itemscope itemtype="https://schema.org/WorkPosition">
                  <h3 itemprop="jobTitle">{{ $jobs[$i]['title'] }}</h3>
                  <h4 itemprop="validFrom">{{ $jobs[$i]['period'] }}</h4>
                  <p><em itemprop="memberOf">{{ $jobs[$i]['company'] }}</em></p>
                  <ul>
                    @foreach($jobs[$i]['details'] as $detail)
                      <li itemprop="description">{{ $detail }}</li>
                    @endforeach
                  </ul>
                </div>
              </div>
            @endfor
          @endif
        </div>
      </div>
    </section>

    <!-- ======= Projects Section ======= -->
    <section id="projects" class="projects section-bg" aria-labelledby="projects-heading">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2 id="projects-heading">Key Projects</h2>
          <p>Featured works and major contributions.</p>
        </div>
        @php
          $projects = [
            [
            'title' => 'Stock Management System',
            'desc' => 'Comprehensive inventory and sales management solution for wholesalers. Features include product purchase & sales tracking, customer due management, SMS notifications, invoice generation, and advanced reporting. The system also supports shop customization, role-based user management, and multilingual interface (Bangla/English).',
            'link' => 'http://shopkeeper.dordambd.com/',
            'link_text' => 'View Demo',
            'badges' => [
                ['text' => 'Laravel', 'class' => 'bg-info text-dark'],
                ['text' => 'MySQL', 'class' => 'bg-warning text-dark'],
                ['text' => 'Bootstrap', 'class' => 'bg-secondary'],
            ]
            ],
          ];
        @endphp
        <div class="row justify-content-center">
          @if(count($projects) == 1)
            <div class="col-md-6 d-flex justify-content-center">
              <div class="project-card w-100" itemscope itemtype="https://schema.org/SoftwareApplication">
                <h3 itemprop="name">{{ $projects[0]['title'] }}</h3>
                <p itemprop="description">{{ $projects[0]['desc'] }}</p>
                <meta itemprop="applicationCategory" content="BusinessApplication">
                <div class="d-flex justify-content-between align-items-center mt-3">
                  <div>
                    @foreach($projects[0]['badges'] as $badge)
                      <span class="badge {{ $badge['class'] }} me-1">{{ $badge['text'] }}</span>
                    @endforeach
                  </div>
                  <div>
                    <a href="{{ $projects[0]['link'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-primary" itemprop="url">{{ $projects[0]['link_text'] }}</a>
                  </div>
                </div>
              </div>
            </div>
          @else
            @foreach($projects as $project)
              <div class="col-md-6">
                <div class="project-card" itemscope itemtype="https://schema.org/SoftwareApplication">
                  <h3 itemprop="name">{{ $project['title'] }}</h3>
                  <p itemprop="description">{{ $project['desc'] }}</p>
                  <meta itemprop="applicationCategory" content="BusinessApplication">
                  <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                      @foreach($project['badges'] as $badge)
                        <span class="badge {{ $badge['class'] }} me-1">{{ $badge['text'] }}</span>
                      @endforeach
                    </div>
                    <div>
                      <a href="{{ $project['link'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-primary" itemprop="url">{{ $project['link_text'] }}</a>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          @endif
        </div>
      </div>
    </section>

    <!-- ======= Resume Section ======= -->
    <section id="resume" class="resume">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2>Education & Certifications</h2>
        </div>
        <div class="row justify-content-center">
          <div class="col-lg-4 mb-4">
            <div class="project-card equal-cardx">
              <div class="edu-title">Masters of Science (Enggr.)</div>
              <div class="edu-period">2024 - 2025</div>
              <table class="edu-table">
                <tr><td><em>Computer Science & Engineering</em></td></tr>
                <tr><td>Uttara University, Dhaka.</td></tr>
              </table>
            </div>
          </div>
          <div class="col-lg-4 mb-4">
            <div class="project-card equal-cardx">
              <div class="edu-title">Bachelor of Science (Enggr.)</div>
              <div class="edu-period">2018 - 2021</div>
              <table class="edu-table">
                <tr><td><em>Computer Science & Engineering</em></td></tr>
                <tr><td>Uttara University, Dhaka.</td></tr>
              </table>
            </div>
          </div>
          <div class="col-lg-4 mb-4">
            <div class="project-card equal-cardx text-center">
                <div class="edu-title">Professional Certification</div>
                <div class="edu-period">2022</div>
              <table class="edu-table">
                <tr><td><em>PHP With Laravel Framework (3 Months)</em></td></tr>
                <tr><td>Skills for Employment Investment Program (SEIP)</td></tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ======= Contact Section ======= -->
    <section id="contact" class="contact section-bg">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2>Get in Touch</h2>
          <p>Let's connect! Feel free to reach out for collaboration, hiring, or just to say hello.</p>
        </div>
        <div class="row mt-1 mb-5">
          <div class="col-lg-4">
        <div class="info">
            <a href="https://maps.google.com/?q=Dhaka, Bangladesh" target="_blank" class="icon-box" style="text-decoration:none; color:inherit;">
                <i class="bi bi-geo-alt"></i>
                <div>
                    <h5>Location</h5>
                    <p>{{ get_setting('present_address') ?? 'Dhaka, Bangladesh' }}</p>
                </div>
            </a>

            <a href="mailto:{{ get_setting('email') ?? 'example@email.com' }}" class="icon-box" style="text-decoration:none; color:inherit;">
                <i class="bi bi-envelope"></i>
                <div>
                    <h5>Email</h5>
                    <p>{{ get_setting('email') ?? 'example@email.com' }}</p>
                </div>
            </a>

            <a href="tel:+88{{ get_setting('mobile') ?? '09696009656' }}" class="icon-box" style="text-decoration:none; color:inherit;">
                <i class="bi bi-phone"></i>
                <div>
                    <h5>Phone</h5>
                    <p>{{ get_setting('mobile') ?? '09696009656' }}</p>
                </div>
            </a>

            <a href="https://t.me/m_estiaQue" target="_blank" class="icon-box mb-0" style="text-decoration:none; color:inherit;">
                <i class="bi bi-telegram"></i>
                <div>
                    <h5>Telegram</h5>
                    <p>@m_estiaQue</p>
                </div>
            </a>
        </div>

          </div>
          <div class="col-lg-8 mt-5 mt-lg-0">
            <div class="contact-form mb-0 h-100">
              <form id="contactForm" action="{{ route('messages.store') }}" method="post" role="form">
                @csrf
                <div class="row">
                  <div class="col-md-6 form-group">
                    <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" required>
                  </div>
                  <div class="col-md-6 form-group">
                    <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required>
                  </div>
                </div>
                <div class="form-group">
                  <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" required>
                </div>
                <div class="form-group">
                  <textarea class="form-control" name="message" id="message" rows="5" placeholder="Message" required></textarea>
                </div>
                <div class="text-center">
                  <button type="submit" id="submitButton" class="btn btn-primary">Send Message</button>
                </div>
              </form>
              <div id="success-animation" class="d-none">
                <div class="checkmark-circle">
                  <div class="background"></div>
                  <div class="checkmark draw"></div>
                </div>
                <h3 class="thank-you-message">Thank you for your message!</h3>
                <p class="follow-up-message">I'll get back to you soon.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <!-- ======= Footer ======= -->
  <footer id="footer" class="bg-white">
    <div class="container py-2">
      <div class="row align-items-center">
        <div class="col-md-6 order-2 order-md-1">
          <div class="copyright">
            Copyright &copy; {{ date('Y') }} <strong>M. Estiaque Ahmed Khan</strong>. All Rights Reserved.
          </div>
          <div class="credits">
            Designed & Developed by <a href="https://mestiaque.com" rel="author">M. Estiaque Ahmed Khan</a>
          </div>
        </div>
        <div class="col-md-6 text-center text-md-end mb-3 mb-md-0 order-1 order-md-2">
          <a href="{{ route('cv') }}" class="btn btn-primary" title="Resume Preview">
            <i class="bi bi-file-earmark-richtext me-2" aria-hidden="true"></i>Resume
          </a>
          <a href="{{ route('gallery.index') }}" class="btn btn-primary" title="View Gallery">
            <i class="bi bi-file-earmark-image me-2" aria-hidden="true"></i>Gallery
          </a>
        </div>
      </div>
    </div>
  </footer>

  <div id="preloader" aria-hidden="true"></div>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center" aria-label="Back to top"><i class="bi bi-arrow-up-short" aria-hidden="true"></i></a>

  <!-- Structured Data - Person -->
    <script type="application/ld+json">
        {!! json_encode([
        '@context' => "https://schema.org",
        "@type" => "Person",
        "name" => "M. Estiaque Ahmed Khan",
        "jobTitle" => "Software Engineer",
        "birthDate" => "1998-12-15",
        "email" => get_setting('email') ?? 'info@mestiaque.com',
        "telephone" => get_setting('mobile') ?? '09696009656',
        "url" => url()->current(),
        "image" => asset('storage/images/profile_image/' . get_setting('profile_image')),
        "sameAs" => array_filter([
            get_setting('facebook_link') ?? null,
            get_setting('twitter_link') ?? null,
            get_setting('linkedin_link') ?? null,
            get_setting('instagram_link') ?? null
        ]),
        "address" => [
            "@type" => "PostalAddress",
            "addressLocality" => "Dhaka",
            "addressCountry" => "Bangladesh"
        ],
        "alumniOf" => [
            [
            "@type" => "CollegeOrUniversity",
            "name" => "Uttara University",
            "sameAs" => "https://uttarauniversity.edu.bd/",
            "location" => "Dhaka, Bangladesh"
            ]
        ],
        "workLocation" => [
            "@type" => "Place",
            "address" => [
            "@type" => "PostalAddress",
            "addressLocality" => "Dhaka",
            "addressCountry" => "Bangladesh"
            ]
        ],
        "worksFor" => [
            "@type" => "Organization",
            "name" => "Isotope IT Ltd.",
            "location" => "Dhaka, Bangladesh"
        ]
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

  <!-- Vendor JS Files -->
  <script src="{{asset('front/vendor/purecounter/purecounter_vanilla.js')}}"></script>
  <script src="{{asset('front/vendor/aos/aos.js')}}"></script>
  <script src="{{asset('front/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('front/vendor/glightbox/js/glightbox.min.js')}}"></script>
  <script src="{{asset('front/vendor/isotope-layout/isotope.pkgd.min.js')}}"></script>
  <script src="{{asset('front/vendor/swiper/swiper-bundle.min.js')}}"></script>
  <script src="{{asset('front/vendor/typed.js/typed.min.js')}}"></script>
  <script src="{{asset('front/vendor/waypoints/noframework.waypoints.js')}}"></script>
  <script src="{{asset('front/vendor/php-email-form/validate.js')}}"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{asset('front/js/main.js')}}"></script>
  <script src="{{asset('front/js/contact.js')}}"></script>
  <script src="{{asset('front/js/custom.js')}}"></script>

  <!-- Add this right before the closing </body> tag -->

  <script>
    $(document).ready(function() {
      // Contact form submission handler
      $('#contactForm').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var submitButton = $('#submitButton');
        var formData = form.serialize();
        var url = form.attr('action');

        // Disable submit button and show loading state
        submitButton.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Sending...');

        $.ajax({
          type: 'POST',
          url: url,
          data: formData,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response) {
            // Hide form and show success animation
            form.slideUp(300, function() {
              $('#success-animation').removeClass('d-none').hide().fadeIn(500);
            });
          },
          error: function(xhr) {
            // Re-enable submit button
            submitButton.prop('disabled', false).html('Send Message');

            // Show error message
            if (xhr.status === 422) {
              // Validation errors
              var errors = xhr.responseJSON.errors;
              $.each(errors, function(key, value) {
                $('#' + key).addClass('is-invalid');
                $('<div class="invalid-feedback">'+value[0]+'</div>').insertAfter('#' + key);
              });
            } else {
              // General error
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong! Please try again later.'
              });
            }
          }
        });
      });


    });
  </script>
{{-- ১. শীতকালীন তুষারপাত (Snowfall Effect) --}}
@include('em_core::effects.snow')

{{-- ২. বিজয়ের মাস ও বিজয় দিবস (ডিসেম্বর) --}}
{{-- @include('em_core::effects.victory') --}}

{{-- ৩. আন্তর্জাতিক মাতৃভাষা দিবস (ফেব্রুয়ারি) --}}
{{-- @include('em_core::effects.language_day') --}}

{{-- ৪. স্বাধীনতা দিবস (২৬শে মার্চ) --}}
{{-- @include('em_core::effects.independence_day') --}}

{{-- ৫. ভালোবাসা দিবস (ভ্যালেন্টাইন ডে) --}}
{{-- @include('em_core::effects.valentine') --}}

{{-- ৬. পহেলা বৈশাখ (বাঙালিয়ানা ও মাছের ইফেক্ট) --}}
{{-- @include('em_core::effects.baishakh') --}}

{{-- ৭. হ্যাপি নিউ ইয়ার --}}
{{-- @include('em_core::effects.new_year') --}}
{{-- @include('em_core::effects.confetti') --}}
</body>
</html>
</html>
