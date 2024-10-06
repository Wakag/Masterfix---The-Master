<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ Page</title>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            
        }

        h1 {
            text-align: center;
            color: #333;
        }

        /* Header Section */
        .faq-header {
            
            padding: 50px 20px;
            text-align: center;
            
        }

        .faq-header h1 {
            font-size: 36px;
            color: #333;
        }

        .faq-header p {
            color: #777;
            margin-top: 10px;
            font-size: 18px;
        }

        .faq-header img {
            margin-top: 20px;
            max-width: 100%;
            height: auto;
        }

        /* Main Container */
        .faq-container {
            display: flex;
            margin: 0 auto;
            align-items: center;

        }

        /* Sidebar (Categories) */
        .faq-sidebar {
            width: 25%;
            padding-right: 20px;
        }

        .faq-sidebar ul {
            list-style: none;
            padding: 0;
        }

        .faq-sidebar li {
            margin-bottom: 15px;
        }

        .faq-sidebar a {
            text-decoration: none;
            font-size: 18px;
            color: #007BFF;
        }

        /* FAQ Content */
        .faq-content {
            
            padding: 50px 20px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            width: 100%;
        }

        .faq-content h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
           
            
        }

        

        .faq-item h3 {
            background-color: #f1f1f1;
            padding: 15px;
            margin: 0;
            cursor: pointer;
            border-radius: 5px;
            color: #333;
        }

        .faq-item p {
            margin: 0;
            padding: 0 15px;
            background-color: #fff;
            display: none;
            border-left: 4px solid #007BFF;
        }

        .faq-item.active p {
            display: block;
            padding: 15px 15px;
            background-color: #f9f9f9;
        }

        td {
            padding: 20px;
            vertical-align: top;
        }
    
        .faq-item {
            width:100%;
        }
       
        .faq-item {
            width: 100%;
            max-width: 1200px; /* Makes it desktop-width */
            margin: 0 auto;
            box-sizing: border-box;
            min-height: 10px; /* Adjust height as per your need */
            background-color: #f9f9f9;
            margin-bottom: 20px;

        }

    </style>
</head>
<body>

<table>
        <!-- Header Section with Title, Description, and Image -->
        <tr>
            <td colspan="2">
                <div class="faq-header">
                    <h1>FAQs</h1>
                    <p>Have questions?<br> Here you'll find the answers most valued by our users, along with helpful instructions and support.</p>
                </div>
            </td>
        

            <td colspan="2" style="text-align: center;">
                <img src="faqpic.gif" alt="FAQ Illustration" style='width :70%;'>
            </td>
        </tr>
    </table>
        <!-- Main FAQ Content Section -->
      
                <div class="faq-container">
                    <div class="faq-content">

                        

                        <!-- FAQ 1 -->
                        <div class="faq-item">
                            <h3>How do I report a maintenance issue?</h3>
                            <p>You can report a maintenance issue by logging into your MasterFix account and clicking on the "Report Issue" button. Fill out the form with the necessary details and submit it. Our team will address the issue as soon as possible.</p>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="faq-item">
                            <h3>What types of maintenance issues does MasterFix handle?</h3>
                            <p>MasterFix handles a wide range of maintenance issues, including plumbing, electrical problems, heating, and air conditioning issues, as well as general repairs and maintenance tasks in university residences.</p>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="faq-item">
                            <h3>How long does it take for a maintenance request to be addressed?</h3>
                            <p>Being part of our platform gives you access to a large customer base and advanced tools to optimize your listings.</p>
                        </div>

                        <!-- FAQ 4 -->
                        <div class="faq-item">
                            <h3>Can I track the status of my maintenance request?</h3>
                            <p>Yes, you can track the status of your request by logging into your MasterFix account. The status will be updated in real-time as our team works on resolving the issue.</p>
                        </div>

                        <!-- FAQ 5 -->
                        <div class="faq-item">
                            <h3>Is there a cost for using MasterFix services?</h3>
                            <p>No, MasterFix services are included as part of your university residence fees, so there is no additional cost for using our services.</p>
                        </div>

                    </div>
                </div>

    <!-- JavaScript to handle collapsible FAQ sections -->
    <script>
        // Select all FAQ headers
        const faqHeaders = document.querySelectorAll('.faq-item h3');

        // Add click event to toggle the display of the answers
        faqHeaders.forEach(header => {
            header.addEventListener('click', () => {
                const parent = header.parentElement;
                parent.classList.toggle('active');
            });
        });
    </script>
</body>
</html>