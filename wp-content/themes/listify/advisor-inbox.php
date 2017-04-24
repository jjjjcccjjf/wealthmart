<?php
/*
Template Name: Advisor Inbox
*/

if(isset($_POST['a_msg'])){
  $data['receiver_id'] = $_POST['receiver_id'];
  $data['sender_id'] = $GLOBALS['current_user']->ID;
  $data['message'] = $_POST['a_msg'];
  $wpdb->insert('advisor_inbox', $data);

  header('Location: ' . site_url('advisor-inbox/?sc=1#'));
  die();

}

$msgs = $wpdb->get_results('SELECT * FROM advisor_inbox WHERE receiver_id = ' . $GLOBALS['current_user']->ID . ' AND type = 0', ARRAY_A); # 0 - standard message
$sys_msgs = $wpdb->get_results('SELECT * FROM advisor_inbox WHERE receiver_id = ' . $GLOBALS['current_user']->ID . ' AND type > 0', ARRAY_A); # 1 - appointment

global $style;

$blog_style = get_theme_mod( 'content-blog-style', 'default' );
$style = 'grid-standard' == $blog_style ? 'standard' : 'cover';

/**
* all $GLOBALs in header
* /classes folder are loaded inside the header
*/
get_header();

?>
<!-- Modal -->
<div class="modal" id="modal-reply" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-header">
      <h2>Reply to <span id="reply_to"></span></h2>
      <a href="#" class="btn-close" aria-hidden="true">×</a>
    </div>
    <div class="modal-body">
      <form method="post">
        <textarea name="a_msg" id="a_msg"rows="8" cols="80" placeholder="Your message..."></textarea>
      </div>
      <div class="modal-footer">
        <input type="hidden" name="receiver_id" id="receiver_id" value=""> <!-- set by javascript -->
        <button type="submit" class="btn-submit">Reply</button>
      </form>
    </div>
  </div>
</div>
<!-- /Modal -->


<div class="container">
  <?php
  if(@$_GET['sc'] == 1){?>
    <div class="sc-msg">Message sent</div>
    <?php }
    ?>

    <div class="row">


      <div class="col-md-6">
        <h2>Inbox</h2>

        <?php

        if(count($msgs) > 0 ):
          foreach($msgs as $msg){

            $first_name = get_user_meta($msg['sender_id'], 'first_name', true);
            $last_name = get_user_meta($msg['sender_id'], 'last_name', true);
            $sender_name = $first_name . " " . $last_name;
            $user_email = get_userdata($msg['sender_id'])->data->user_email

            ?>
            <div class="inbox-card" >
              <p>
                From: <span id="name_<?= $msg['id'] ?>"><?php echo $sender_name ?></span>
                <a href="mailto:<?php echo $user_email ?>">&lt;<?php echo $user_email ?>&gt;</a>
              </p>
              <hr>
              <p><?php echo $msg['message'] ?></p>
              <input type="hidden" id="receiver_<?= $msg['id'] ?>" value="<?= $msg['sender_id'] ?>">
              <hr>
              <a href="#modal-reply" onclick="setReply('<?= $msg['id'] ?>')" class="reply-btn">Reply</a>
            </div>

            <?php }
          else:
            ?>
            <div class="inbox-card" >
              <p>
                No new messages.
              </p>
            </div>
          <?php endif;
          ?>
        </div>

        <div class="col-md-6">
          <h2>
            System Messages
            <br>
            <sub>Appointments &amp; Announcements</sub>
          </h2>

          <?php

          if(count($sys_msgs) > 0 ):
            foreach($sys_msgs as $msg){

              $first_name = get_user_meta($msg['sender_id'], 'first_name', true);
              $last_name = get_user_meta($msg['sender_id'], 'last_name', true);
              $sender_name = $first_name . " " . $last_name;
              $user_email = get_userdata($msg['sender_id'])->data->user_email

              ?>
              <div class="inbox-card" >
                <p>From: System</p>
                <span style="display:none" id="name_<?= $msg['id'] ?>"><?php echo $sender_name ?></span>
                <hr>
                <p><?php echo $msg['message'] ?></p>
                <input type="hidden" id="receiver_<?= $msg['id'] ?>" value="<?= $msg['sender_id'] ?>">
                <?php if($msg['type'] == 1):?>
                  <hr>
                  <a href="#modal-reply" onclick="setReply('<?= $msg['id'] ?>')" class="reply-btn">Reply</a>
                <?php endif; ?>
              </div>

              <?php }
            else:
              ?>
              <div class="inbox-card" >
                <p>No new messages.</p>
              </div>
            <?php endif;
            ?>
          </div>


        </div> <!-- end of row container -->


        <?php get_footer();?>

        <script type="text/javascript">
        $(document).ready(function(){
          setReply = function(id){
            var sender_name = $("#name_" + id).text();
            var receiver_id = $("#receiver_" + id).val();
            $("#receiver_id").val(receiver_id);
            $("#reply_to").text(sender_name);
          }
        })
        </script>
