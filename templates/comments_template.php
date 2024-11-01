<?php 
$so_thread = so_get_thread_url();
$so_shortname = so_get_shortname();
$sso = get_option('so_sso_data');
if (!!$so_shortname){
?>
<div class="so_comments" 
    <?php echo SO_SERVER?>
    data-sitename="<?php echo $so_shortname; ?>" 
    data-thread_url="<?php echo $so_thread; ?>"
    <?php if($sso){?>data-encrypted="<?php echo $sso?>"<?php }?>
></div>
<script src="<?php echo SO_API_URL?>widget/embed.js" async="async"></script>
<a href="http://api.solidopinion.com/seo/<?php echo $so_shortname; ?><?php echo $so_thread; ?>seo.html" style="font-size:10px;"><?php echo __('Сomments аrchive');?></a>
<?php } 