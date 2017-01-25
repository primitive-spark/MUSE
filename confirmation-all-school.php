<?php
/** Confirmation Page for MUSE Open House Ad Words Campaign

This file saves the POST data of a form into the WP database for
future use.

It also is going to tigger an email to the user who submitted the form.

It also sends an email to an admin at MUSE so they know someone submitted
the form.


**/
require( '../wp-load.php' );
include_once( '../wp-config.php');

global $wpdb;
$ip = $_SERVER['REMOTE_ADDR'];
$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));
$zipcode = "{$details->postal}";
$city = "{$details->city}";
$state = "{$details->region}";


/** Grab the POST Data **/
$table_name = 'wp_muse_openhouse_leads_data';
//Default date
$date = date('M j, Y');
//Highschool Tour Campaign
$campaign = 'All School Tour';
if(isset($_POST['date']))$date = $_POST['date'];
if(isset($_POST['phone']))$phone = $_POST['phone'];
if(isset($_POST['email']))$email = $_POST['email'];
if(isset($_POST['fname']))$fname = $_POST['fname'];
if(isset($_POST['lname']))$lname = $_POST['lname'];
if(isset($_POST['gclid']))$gclid = $_POST['gclid'];

/* Age Groups */
$ages = array('Kindergarten', 'High School'); //Default
if(isset($_POST['age']))$ages = $_POST['age'];
$ageArr = $ages;
// If theres more than one choice, create a comma seperated list of ages
if(count($ages) > 1){
	$ages = implode(',', $ages);
} else {
	$ages = $ages[0];
}

$googleForm = "https://docs.google.com/a/primitivespark.com/forms/d/1e2H4nvDBahBecI9SHGrnCGFfXURccT3fiEfWDX4Wcwc/viewform?";

/** Insert them into the DM **/
if($_POST['email']){
$wpdb->insert( 
	$table_name, 
	array( 
		'Date' => $date, 
		'FirstName' => $fname, 
		'LastName' => $lname, 
		'Email' => $email,
		'Phone' => $phone,
		'Campaign' => $campaign,
		'Level' => $ages,
        'ZipCode' => $zipcode,
        'gclid' => $gclid,
	) 
);
if((bool) array_intersect(array('Early Childhood Education', 'Kindergarten', 'Elementary School'), $ageArr)){
    $level = "Prime%20Campus%20-%20December%203rd,%202016%20-%2010:00am";
    
    if((bool) array_intersect(array('Middle School', 'High School'), $ageArr)){
      $level = "I%20have%20children%20that%20would%20be%20on%20both%20campuses.%20I%20will%20attend%20both.";  
    }
    
} else {
    $level = "Middle/High%20Campus%20-%20December%203rd,%202016%20-%201:00pm";
}
//Append Google Form Values
$googleForm .= "&entry.715004519=".$fname; //First Name
$googleForm .= "&entry.709305022=".$lname; //Lasst Name
$googleForm .= "&entry.1133109220=".$email; //Email
//$googleForm .= "&entry.477331050=".$level; //Grade Level
$googleForm .= "&entry.1700774550=".$zipcode; //Zipcode Level
$googleForm .= "&entry.1990789861=".$city; //City
$googleForm .= "&entry.1666629870=".$state; //State
$googleForm .= "&entry.1443593191=Google Ads"; //Google Ads Option
$googleForm .= ($phone != "" ? "&entry.854537827=".$phone : ""); //Phone

}

/** Generate Auto Emails **/
if($_POST['email']){
	
	/** To Admin **/
	$emailHeaders = "Reply-To: noreply@museschool.org\r\n";
	$emailHeaders .= "Return-Path: noreply@museschool.org\r\n";
	$emailHeaders .= "From: MUSE <noreply@museschool.org>\r\n";
	$emailHeaders .= 'Signed-by: museschool.org\r\n"';
	$emailHeaders .= 'MIME-Version: 1.0' . "\r\n";
	$emailHeaders .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	//$toEmail = 'bexnowski@primitivespark.com';
	$toEmail = "mcordish@museschool.org";
	$content = "Someone has submitted a request for a Tour!<br /><br />";
	$content .= "Name: ".$fname. " ". $lname . "<br />";
	$content .= "Email: ".$email."<br />";
	$content .= "Level(s): ".$ages."<br />";
	if($phone){
		$content .= "Phone: ".$phone."<br />";
	}

	mail( $toEmail, 'Tour Form Submission', $content, $emailHeaders );
	
	/** To User **/
	$emailHeaders = "Reply-To: admissions@museschool.org\r\n";
	$emailHeaders .= "Return-Path: admissions@museschool.org\r\n";
	$emailHeaders .= "From: Tamika Davis of MUSE School <admissions@museschool.org>\r\n";
	$emailHeaders .= 'Signed-by: museschool.org\r\n"';
	$emailHeaders .= 'MIME-Version: 1.0' . "\r\n";
	$emailHeaders .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";	
	//$email = 'bexnowski@primitivespark.com';
	//$email = "info@museschool.org";
	$content = "Dear ".$fname.",<br /><br />";
	$content .= "Thank you for signing up for the MUSE High School Open House on ".$date.". We are located at 4345 N Las Virgenes Road, Calabasas, CA 91302. We look forward to showing you and your family what makes MUSE School so special!";
	$content .= "<br /><br />";
	$content .="Best,<br />Tamika Davis<br />Director of Admissions";
	
	//mail( $email, "You're registered for a MUSE High School Open House", $content, $emailHeaders );
}


?>
<!doctype html>
<html>
    <head>
        <title>MUSE - Tour Sign-up - Thank You</title>
        
        <!-- meta -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=1, minimum-scale=1, maximum-scale=1">
        
        <!-- icons -->
		<link href="img/favicon.ico" rel="shortcut icon">
		<link href="img/touch.png" rel="apple-touch-icon-precomposed">
		
        <!-- stylesheets -->
        <link rel="stylesheet" href="css/normalize.min.css">
        <link rel="stylesheet" href="css/style.css">
        
        <!-- font awesome css -->
        <link href="http://netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css" rel="stylesheet">
		
        <!-- load modernizr -->
        <script src="js/modernizr-2.6.2-respond-1.1.0.min.js"></script>
        <!-- load jQuery from Goolge CDN -->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <style>
            ul.has-bullets,
            ul.has-bullets ul{
                list-style:inside;
                margin:5px 15px 15px;
            }
        </style>
    </head>
    <body class="confirmation">
    	<header class="clear">
        	<a href="http://www.museschool.org" class="logo left" target="_blank"><img src="img/logo.png" alt="MUSE" /></a>
            <div class="tagline right hideMobile">
            	<!--<span class="hideMobile">Call us at</span> <a href="tel:18188805437">+1 818.880.5437</a> -->
                Inspiring and preparing young people to live consciously with themselves, one another, and the planet.
            </div>
        </header>
        <div class="wrapper clear">
        	<section>
            	<h1>Thank you for reaching out to MUSE School!</h1>
                <p>We are delighted that you are interested in learning more about MUSE School. We’ll be reaching out to you to answer any questions you may have. In the meantime, select a campus tour date now:</p>
                   <!--
                    <?php

                            if((bool) array_intersect(array('Early Childhood Education', 'Kindergarten', 'Elementary School'), $ageArr)): ?>                                
                                <ul>
                                    <h3>Saturday, December 3, 2016 at 10:00 am</h3>
                                    <li>Early Childhood Education to 5th grade</li>
                                    <li>MUSE Prime Campus</li>
                                    <li>1666 Las Virgenes Canyon Rd, Calabasas, CA 91302</li>
                                </ul>
                            <?php endif;

                            if((bool) array_intersect(array('Middle School', 'High School'), $ageArr)): ?>
                                <ul>
                                    <h3>Saturday, December 3, 2016 at 1:00 pm</h3>
                                    <li>6th - 12th grade</li>
                                    <li>MUSE Middle/High Campus</li>
                                    <li>4345 Las Virgenes Rd, Calabasas, CA 91302</li>
                                </ul>
                            <?php endif; ?>
                            -->
                <a href="<?php echo $googleForm; ?>" class="button mar-t-b inline-block" target="_blank" title="Choose Tour Date">Choose Tour Date</a>
                <!--
                <h2>Stay Connected with MUSE School</h2>
                <ul class="social">
                	<li class="facebook"><a href="https://www.facebook.com/MUSESchoolCA" target="_blank" title="Facebook">Like us on Facebook</a></li>
                    <li class="twitter"><a href="https://twitter.com/MUSESchoolCA" target="_blank" title="Twitter">Follow us on Twitter</a></li>
                    <li class="youtube"><a href="https://www.youtube.com/user/MUSEschoolCAvideo" target="_blank" title="YouTube">Watch our YouTube videos</a></li>
                </ul> -->
            </section>
        </div><!-- end wrapper -->
        <div class="location-wrap clear">
        	<h2>Where is MUSE School?</h2>
            <h3>We have two breathtaking campuses nestled in the Santa Monica Mountains:</h3>
            <img src="img/maps.jpg" width="100%" class="hideMobile" alt="MUSE School"/>
            <div class="location left">
            	<h3>location <span>A</span></h3>
                <strong>Middle & High School campus (6<sup>th</sup> - 12<sup>th</sup> grade)</strong><br />
                <span>4345 Las Virgenes Rd, Calabasas, CA 91302</span>
            </div>
            <div class="location two left">
            	<h3>location <span>B</span></h3>
                <strong>Early Childhood Education - 5<sup>th</sup> grade campus</strong><br />
                <span>1666 Las Virgenes Rd, Calabasas, CA 91302</span>
            </div>
        </div><!-- end location -->
		<footer class="clear">
        	<a href="javascript:void(0);" title="Back to top" class="toTop showMobile">Back to top</a>
			<div class="tagline showMobile">
                Inspiring and preparing young people to live consciously with themselves, one another, and the planet.
            </div>
        	<!-- Attach scripts -->   
            <script src="js/scripts.js"></script>     
            <script>
			//jQuery UI controls for Select Menu
            $(document).ready(function () {
				//Back to top scroller
				$('.toTop').on('click', function(){
					$('html, body').animate({scrollTop : 0},800);
				});
				
				//Scroll to web form
				$('.toForm').on('click', function(){
					var offset = $('aside').offset();
					var top = offset.top;
					$('html, body').animate({scrollTop : top},800);
				});

			});
        	</script>
            <script>
			  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			
			  ga('create', 'UA-43093739-1', 'auto');
			  ga('send', 'pageview');
			
			</script>
            
            <!-- Google Code for Sign up for more info Conversion Page -->
			<script type="text/javascript">
            /* <![CDATA[  */
          	var google_conversion_id = 963535116;
            var google_conversion_language = "en";
            var google_conversion_format = "1";
            var google_conversion_color = "ffffff";
            var google_conversion_label = "lGycCO6LpVcQjMK5ywM";
            var google_remarketing_only = false;
            /* ]]> */
            </script>
            <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
            </script>
            <noscript>
            <div style="display:inline;">
            <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/963535116/?label=lGycCO6LpVcQjMK5ywM&amp;guid=ON&amp;script=0"/>
            </div>
            </noscript>
        </footer><!-- end footer -->
    </body>
</html>
