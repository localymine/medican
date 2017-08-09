<?php
/*
  Template Name: Registration template
 */
?>

<?php get_header(); $options = get_option(AZEXO_FRAMEWORK); ?>
<script src="jquery.min.js"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<div class="container">
    <div id="primary" class="content-area">
        <?php        
        if ($options['show_page_title']) {
            get_template_part('template-parts/general', 'title');
        }
        ?>
        <div id="content" class="site-content" role="main">
          <form class="userform" id="userForm" method="post" action="">
                <p>
                    <label for="name">Name</label>
                    <input id="name" name="name" type="text" >
                </p>
                <p>
                    <label for="email">E-Mail</label>
                    <input id="email" type="email" name="email" >
                </p>
                <p>
                    <label for="phone">Phone</label>
                    <input id="phone" type="phone" name="phone" >
                </p>
               
                
                <p>
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password">
                </p>
              
                <p>
                    <input class="submit" type="submit" value="Submit">
                </p>
            </form>                                                         

          <?php while (have_posts()) : the_post(); ?>
                <div id="post-<?php the_ID(); ?>" <?php post_class('entry'); ?>>
                    <div class="entry-content">
                        <?php the_content(); ?>
                        <?php wp_link_pages(array('before' => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'medican') . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>')); ?>
                    </div><!-- .entry-content -->
                </div><!-- #post -->
                <?php
                if (isset($options['comments']) && $options['comments']) {
                    if (comments_open()) {
                        comments_template();
                    }
                }
                ?>
            <?php endwhile; ?>
        </div><!-- #content -->
    </div><!-- #primary -->
</div>
<?php get_footer(); ?>

<script>
    var $=jQuery.noConflict();
     $(document).ready(function() {
 $("#userForm").validate({
        rules: {
            name: "required",
            email: {
                required: true,
                email: true
            },
            phone: {
                required: true,
                number: true
            },
           
            password: {
                required: true,
                minlength: 6
            },
           
            agree: "required"
        },
        messages: {
            name: "Please enter your name",
            email: "Please enter a valid email address",
            phone: {
                required: "Please enter your phone number",
                number: "Please enter only numeric value"
            },
           
            password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 6 characters long"
            },
           
            agree: "Please accept our policy"
        }
    });
//                    $.ajax({
//						
//	                 type: "POST",
//	                 url: "<?php // echo get_permalink(1601); ?>",
//	                 data:{meta_key:'111'},
//	                 success: function(result){ 
//					  if(result){
//                                              alert(result);
//                                           
//				       
//					 }
//                         }
//	               }); 
     });

 </script>


<style>
.userform{width: 400px;}
.userform p {
    width: 100%;
}
.userform label {
    width: 120px;
    color: #333;
    float: left;
}
input.error {
    border: 1px dotted red;
}
label.error{
    width: 100%;
    color: red;
    font-style: italic;
    margin-left: 120px;
    margin-bottom: 5px;
}
.userform input.submit {
    margin-left: 120px;
}

</style>