<form id="post_form">
<input type="text" name="message" value="This is a message"/>
<input type="text" name="link" />
<input type="text" name="role_id" value="0"/>

</form>

    <div class="r pagecontent">
        <div id="feeds">
            <?php if(trim($feeds)){ ?>
            <?php echo $feeds ?>
            <?php } else {?>
                <div class="vpad10">
                    No feed
                </div>
            <?php }?>
        </div>
        <?php if(trim($feeds) && ($total_feeds > 5)){ ?>
        <div class="show_more ">
            <a id="more_feeds">show older feeds &#x25BC;</a>
        </div>
        <?php } ?>
        <div id="feed_event"></div>
    </div>
    
<script type="text/javascript">
new verticalScroll({
    $link : $('#more_feeds'), 
    action : 'feeds',
    start : 6,
    controller : 'feed',
    $appendTO: $('#feeds') //Must Be Id  to which you want to append
});
</script>
