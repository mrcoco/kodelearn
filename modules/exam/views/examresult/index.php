<div class="r pagecontent">
    <div class="pageTop">
        <div class="pageTitle l">replace_here_page_title</div>
        <div class="pageDesc r">replace_here_page_description</div>
        <div class="clear"></div>
    </div><!-- pageTop -->
    
    <div class="sectionTitle">View Marksheet for</div>
    
    <ul>
        <?php foreach($examgroup as $group){ ?>
        <li class="vm10 "><a href="<?php echo URL::base() . 'exammarksheet/details/examgroup_id/' . $group->id ?>"><?php echo $group->name?></a></li>
        <?php }?>
    </ul>
    
</div>