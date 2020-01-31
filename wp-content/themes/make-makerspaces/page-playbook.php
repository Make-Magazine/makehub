<?php
/**
 * Template Name: Playbook
 *
 * @package _makerspaces
 */

get_header(); ?>

<div class="playbook-page">

  <div class="container">

    <div class="row">

      <div class="col-xs-12 playbook-header">

        <h1><?php echo get_the_title(); ?></h1>   
		  <?php echo the_content(); ?>   

      </div>

    </div>

  </div>

  <div class="playbook-page-hero" style="background: url(/wp-content/themes/makerspaces/img/home-header.png) no-repeat center center;"></div>

  <div class="container playbook-page-body">

    <div class="playbook-cover">
      <img src="/wp-content/themes/makerspaces/img/Playbook-cover.png" alt="Makerspace playbook PDF cover" />
    </div>

    <div class="row">

      <div class="col-xs-12 col-sm-6 playbook-page-body-l">
        <p class="lead">Are you planning to start a makerspace at your school or in your community? Let the Makerspace Playbook guide you with expert advice and helpful strategies to make it a success.</p>
        <p class="lead">Enter your name and email to download the PDF for free.</p>
        <?php
          $isSecure = "http://";
          if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $isSecure = "https://";
          }
        ?>
        <form class="playbook-sub-form" action="https://secure.whatcounts.com/bin/listctrl" method="POST" class="validate" target="_blank">
          <input type="hidden" name="slid" id="list_6B5869DC547D3D46B52F3516A785F101_yes" class="slid" value="6B5869DC547D3D467B33E192ADD9BE4B" /><!-- MakerPro -->
          <input type="hidden" name="cmd" value="subscribe" />
          <input type="hidden" name="custom_source" value="makerspace-playbook" />
          <input type="hidden" name="custom_incentive" value="playbookPDF" />
          <input type="hidden" name="custom_url" value="<?php echo $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; ?>" />
          <input type="hidden" id="format_mime" name="format" value="mime" />
          <input type="hidden" name="custom_host" value="<?php echo $_SERVER["HTTP_HOST"]; ?>" />
			 <label for="name">Your name</label>
          <input name="name" placeholder="Enter your name" required type="name"><label for="email">Your email</label>
          <input name="email" placeholder="Enter your email" required type="email"><br>
          <input value="Download the Makerspace Playbook" class="btn blue-btn btn-block" type="submit">
			  <i>You will receive occasional news which you can opt out of at any time.</i>
        </form>
         <!-- reCaptcha element -->
         <div id="recaptcha" class="g-recaptcha"
            data-sitekey="6Lf_-kEUAAAAAHtDfGBAleSvWSynALMcgI1hc_tP"
            data-callback="playbookSignup"
            data-size="invisible">
         </div>
		</div>
		 
      <div class="col-xs-12 col-sm-6 playbook-page-body-r">

        <p>CONTENTS</p>
        <p><strong>Beginnings</strong><br />
          what we're doing and why, origins of the Maker movement</p>

        <p><strong>Places</strong><br />
          making a space more conducive to a community that makes together</p>

        <p><strong>Tools &amp; Materials</strong><br />
          inventory, budgets, and strategies (see also High School Makerspace Tools &amp; Materials: a companion document detailing the uses and costs of a fully stocked inventory for an in-school Makerspace.)</p>

        <p><strong>Safety</strong><br />
          planning for safety, signage, and common rules</p>

        <p><strong>Roles</strong><br />
          what teachers, students, shop managers, and mentors do in a Makerspace</p>

        <p><strong>Practices</strong><br />
          pedagogical approaches experienced makers use to support emerging makers</p>

        <p><strong>A Year of Making</strong><br />
          teacher Aaron Vanderwerff describes his experience making with students</p>

        <p><strong>Projects</strong><br />
          guiding novice makers as they build their skill set; sources for projects</p>

        <p><strong>Startup</strong><br />
          nuts and bolts of getting involved with the Makerspace network</p>

        <p><strong>Documenting</strong><br />
          sharing projects … and the stories behind their making</p>

        <p><strong>Snapshots</strong><br />
          four school-based Makerspaces in action</p>

        <p><strong>Resources</strong><br />
          helpful lists, forms, and templates</p>

<!--         <p class="small">We welcome your <a href="#" target="_blank">feedback</a> on the kinds of things we should add to this Playbook, what you think we get right and wrong, and any changes you'd make in general.</p>
 -->      </div>

    </div>

  </div>
	
	<div class="container-fluid light-blue">
	  <div class="container">
			<div class="row">
				<div class="col-md-4 col-sm-4 col-xs-12 makerspace-bottom-nav">
					<h4>Join our global network of makerspaces</h4>
					<a href="/register"><button class="btn blue-btn">Add your makerspace</button></a>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12 makerspace-bottom-nav">
					<h4>See an error or need to update your info?</h4>
					<a href="/edit-your-makerspace"><button class="btn blue-btn">Manage your listing</button></a>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-12 makerspace-bottom-nav">
					<h4>Start your own makerspace today</h4>
					<a href="https://learn.make.co/courses/starting-a-school-makerspace/info"><button class="btn blue-btn">Take the course</button></a>
				</div>
			</div>
		</div>
  </div>  

</div>

<a target="_blank" style="display:none;" id="playbook_dl" href="/wp-content/themes/makerspaces/img/Makerspace-Playbook.pdf">Makerspace Playbook</a>


<?php get_footer(); ?>