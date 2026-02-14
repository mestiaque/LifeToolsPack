<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>M. Estiaque Ahmed Khan | CV</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="{{ asset('assets/img/favicon/Encodex.ico') }}" type="image/x-icon">
        <style>
            body {
                font-family: 'Poppins', Arial, sans-serif;
                color: #222;
                background: #fff;
                margin: 0;
                padding: 0;
            }
            .cv-container {
                max-width: 800px;
                margin: 30px auto;
                background: #fff;
                box-shadow: 0 0 10px #e0e0e0;
                padding: 40px 32px 32px 32px;
            }
            .cv-header {
                display: flex;
                align-items: flex-start; /* Changed from center to flex-start */
                border-bottom: 2px solid #e9ecef;
                padding-bottom: 16px;
                margin-bottom: 24px;
            }
            .cv-header-details {
                flex-grow: 1;
            }
            .cv-header-details h1 {
                font-size: 2.2rem;
                font-weight: 700;
                margin: 0 0 8px 0;
                color: #005fa3;
            }
            .cv-header-details p {
                margin: 4px 0;
                font-size: 1rem;
            }
            .cv-photo {
                max-width: 100px;
                height: auto;
                max-height: 120px;
                object-fit: contain;
                margin-left: 32px;
                box-shadow: 0 2px 8px #e0e0e0;
            }
            .cv-section {
                margin-bottom: 28px;
            }
            .cv-section-title {
                font-size: 1.15rem;
                font-weight: 600;
                color: #005fa3;
                margin-bottom: 8px;
                border-bottom: 1px solid #e9ecef;
                padding-bottom: 2px;
                letter-spacing: .5px;
            }
            table.cv-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 1rem;
                margin-bottom: 10px;
            }
            table.cv-table th, table.cv-table td {
                border: 1px solid #e9ecef;
                padding: 7px 10px;
                text-align: left;
            }
            table.cv-table th {
                background: #f6fbff;
            }
            ul {
                margin: 8px 0 0 18px;
            }
            .cv-footer {
                margin-top: 60px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .cv-signature {
                height: 40px;
                margin-bottom: -18px;
                margin-left: 24px;
            }
            @media print {
                body {
                    background: #fff;
                }
                .cv-container {
                    box-shadow: none;
                    margin: 0;
                    padding: 0 18px;
                }
                .cv-photo {
                    max-height: 100px; /* Reduce height when printing */
                }
                .cv-footer, .cv-signature {
                    page-break-inside: avoid;
                }
                #print-btn,
                #show-signature-label,
                #cv-date-input {
                    display: none !important;
                }
            }
        </style>
    </head>
    <body>
        <div class="cv-container">
            <!-- Print Button -->
            <div style="text-align:right; margin-bottom:18px;" id="print-btn">
                <button type="button" onclick="window.print()" style="background:#00b1f5;color:#fff;border:none;padding:7px 18px;border-radius:6px;font-weight:600;cursor:pointer;">
                    Print CV
                </button>
            </div>
            <!-- Header -->
            <div class="cv-header">
                <div class="cv-header-details">
                    <h1>M. Estiaque Ahmed Khan</h1>
                    <p><strong>Address:</strong> {{ get_setting('present_address') }}</p>
                    <p><strong>Mobile:</strong> 01796-00 96 56</p>
                    <p><strong>Email:</strong> <a href="mailto:mestiaquekhan1998@gmail.com">mestiaquekhan1998@gmail.com</a></p>
                    {{-- <p><strong>LinkedIn:</strong> <a href="https://www.linkedin.com/in/mestiaque98" target="_blank">www.linkedin.com/in/mestiaque98</a></p> --}}
                </div>
                <div>
                    <img src="{{ asset('storage/images/profile_image/' . get_setting('profile_image')) }}" alt="Profile" class="cv-photo">
                </div>
            </div>

            <!-- Career Summary -->
            <div class="cv-section">
                <div class="cv-section-title">Career Summary</div>
                <p>
                    Software Engineer with expertise in PHP (Laravel), JavaScript, and Bootstrap. Experienced in developing scalable web applications, RESTful APIs, and team collaboration. Looking for opportunities to contribute technical and interpersonal skills for organizational growth.
                </p>
            </div>

            <!-- Experience -->
            <div class="cv-section">
                <div class="cv-section-title">Professional Experience</div>
                <table class="cv-table">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Designation</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Isotope IT Ltd., Dhaka</td>
                            <td>Software Engineer</td>
                            <td>April 2024 – Present</td>
                        </tr>
                        <tr>
                            <td>Barcodetech Automation Ltd., Dhaka</td>
                            <td>Software Developer</td>
                            <td>September 2022 – March 2024</td>
                        </tr>
                    </tbody>
                </table>
                <ul>
                    <li>Developed dynamic websites and enterprise software solutions.</li>
                    <li>Built scalable Laravel backends with interactive front-end features.</li>
                    <li>Gathered client requirements and delivered tailored solutions.</li>
                    <li>Optimized performance, security, and deployment processes.</li>
                    <li>Integrated barcode scanning with custom software modules.</li>
                </ul>

            </div>

            <!-- Projects -->
            <div class="cv-section">
                <div class="cv-section-title">Projects</div>
                    <ul>
                        <li><strong>Stock Management System:</strong> Inventory and sales management with purchase/sales tracking, customer due, invoicing, reporting, SMS, role/user control, and multilingual support (BN/EN).</li>
                        <li><strong>Document Management System:</strong> Secure document upload, sharing, and access control built with Laravel & Bootstrap.</li>
                    </ul>
            </div>

            <!-- Academic Qualification -->
            <div class="cv-section">
                <div class="cv-section-title">Academic Qualification</div>
                <table class="cv-table">
                    <thead>
                        <tr>
                            <th>Exam Title</th>
                            <th>Major</th>
                            <th>Institute</th>
                            <th>Result</th>
                            <th>Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>M.Sc. in Computer Science & Engineering</td>
                            <td>CSE</td>
                            <td>Uttara University</td>
                            <td>CGPA: 2.70/4.00</td>
                            <td>2025</td>
                        </tr>
                        <tr>
                            <td>B.Sc. in Computer Science & Engineering</td>
                            <td>CSE</td>
                            <td>Uttara University</td>
                            <td>CGPA: 3.07/4.00</td>
                            <td>2021</td>
                        </tr>
                        <tr>
                            <td>HSC</td>
                            <td>Science</td>
                            <td>Patul Hapania High School & College</td>
                            <td>GPA: 3.17/5.00</td>
                            <td>2017</td>
                        </tr>
                        <tr>
                            <td>SSC</td>
                            <td>Science</td>
                            <td>Madhabpur High School</td>
                            <td>GPA: 4.72/5.00</td>
                            <td>2015</td>
                        </tr>
                    </tbody>
                </table>
                <p><strong>External Course:</strong> PHP With Laravel Framework (3 Months)</p>
            </div>

            <!-- Technical Skills -->
            <div class="cv-section">
                <div class="cv-section-title">Technical Skills</div>
                <ul>
                    <li><strong>Frontend:</strong> HTML, CSS, JavaScript, Bootstrap</li>
                    <li><strong>Backend:</strong> PHP, Laravel, Ajax</li>
                    <li><strong>Database:</strong> MySQL</li>
                    <li><strong>Tools:</strong> Git, MS-Office</li>
                    <li><strong>OS:</strong> Windows, Linux</li>
                </ul>
            </div>

            <!-- Personal Strength -->
            <div class="cv-section">
                <div class="cv-section-title">Personal Strengths</div>
                <ul>
                    <li>Positive attitude, resourceful and hardworking</li>
                    <li>Effective communication and teamwork skills</li>
                    <li>Problem-solving ability, ability to handle deadlines</li>
                    <li>Capable of independent work and leadership</li>
                    <!-- * Add more if needed -->
                </ul>
            </div>

            <!-- Languages & Interests -->
            <div class="cv-section">
                <div class="cv-section-title">Languages & Interests</div>
                <table class="cv-table" style="margin-bottom: 0;">
                    <tbody>
                        <tr>
                            <td><strong>Languages</strong></td>
                            <td>Bangla: Native proficiency; English: Professional working proficiency</td>
                        </tr>
                        <tr>
                            <td><strong>Interests</strong></td>
                            <td>Reading, Traveling, Tech Blogs, Exploring Internet, Badminton</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Personal Details -->
            <div class="cv-section">
                <div class="cv-section-title">Personal Details</div>
                <table class="cv-table" style="margin-bottom: 0;">
                    <tbody>
                        <tr>
                            <td>Father’s Name</td>
                            <td>Md. Noor Alahi</td>
                        </tr>
                        <tr>
                            <td>Mother's Name</td>
                            <td>Mst. Esme Tara Khanom</td>
                        </tr>
                        <tr>
                            <td>Date of Birth</td>
                            <td>December 15, 1998</td>
                        </tr>
                        <tr>
                            <td>Gender</td>
                            <td>Male</td>
                        </tr>
                        <tr>
                            <td>Marital Status</td>
                            <td>Unmarried</td>
                        </tr>
                        <tr>
                            <td>Nationality</td>
                            <td>Bangladeshi</td>
                        </tr>
                        <tr>
                            <td>Religion</td>
                            <td>Islam</td>
                        </tr>
                        <tr>
                            <td>Permanent Address</td>
                            <td>Village: Banshila, P/O: Patul, P/S: Naldanga, Natore-6403.</td>
                        </tr>
                        <tr>
                            <td>Current Address</td>
                            <td>{{ get_setting('present_address') }}</td>
                        </tr>
                        <tr>
                            <td>Blood Group</td>
                            <td>O+ (Positive)</td>
                        </tr>
                        <!-- * NID, Signature, others if strictly needed -->
                    </tbody>
                </table>
            </div>

            <!-- Declaration -->
            <div class="cv-section">
                <div class="cv-section-title">Declaration</div>
                <p>
                    I hereby declare that the information provided is true and accurate to the best of my knowledge. Supporting documents/certificates can be presented on request.
                </p>
            </div>

            <!-- Footer -->
            <div class="cv-footer">
                <div>
                    <label id="show-signature-label" style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                        <input type="checkbox" id="show-signature">
                        Attach Signature
                    </label>
                    <span id="signature-img-wrap" style="display: none;">
                        <img src="{{ asset('storage/images/signature_image/' . get_setting('signature_image')) }}" alt="Signature" class="cv-signature">
                    </span>
                    <hr style="margin: 5px 0; border: none; border-top: 1px solid #999; width: 100%;">
                    <div style="font-weight:600;">M. Estiaque Ahmed Khan</div>
                </div>
                <div style="display: flex; flex-direction: column; justify-content: flex-end; height: 100%;">
                    <input type="date" id="cv-date-input" name="cv_date"
                        value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                        style="padding:4px 10px;border-radius:5px;border:1px solid #e0e0e0;margin-left:8px;">
                    <span id="cv-date-display" style="font-weight:500;">
                        {{ \Carbon\Carbon::now()->format('F d, Y') }}
                    </span>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var checkbox = document.getElementById('show-signature');
                var signatureWrap = document.getElementById('signature-img-wrap');
                var dateInput = document.getElementById('cv-date-input');
                var dateDisplay = document.getElementById('cv-date-display');
                checkbox && checkbox.addEventListener('change', function() {
                    signatureWrap.style.display = this.checked ? 'inline' : 'none';
                });
                dateInput && dateInput.addEventListener('change', function() {
                    if (dateInput.value) {
                        var dateObj = new Date(dateInput.value);
                        var options = { day: '2-digit', month: 'long', year: 'numeric' };
                        dateDisplay.textContent = dateObj.toLocaleDateString('en-US', options);
                    }
                });
            });
        </script>
    </body>
</html>

