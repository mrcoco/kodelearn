    <div class="r pagecontent">
        <div class="pageTop">
            <div class="pageTitle l">Courses</div>
            <div class="pageDesc r">this is a test description this is a test description this is a test description this is a test description this is a test description </div>
            <div class="clear"></div>
        </div><!-- pageTop -->
        
        <div class="topbar">
            <?php echo $links['add']?>
            <a href="#" class="pageAction l">Send message</a>
            <a onclick="$('#course').submit();" class="pageAction r alert">Delete selected...</a>
            <span class="clear">&nbsp;</span>
        </div><!-- topbar -->
        
        <form name="course" id="course" method="POST" action="<?php echo $links['delete'] ?>">
        <table class="vm10 datatable fullwidth">
            <?php echo $table['heading'] ?>
            <tr class="filter">
                <td><input type="hidden" id="filter_url" value="<?php echo $filter_url ?>" /></td>
                <td><input type="text" name="filter_name" value="<?php echo $filter_name ?>" /></td>
                <td><input type="text" name="filter_access_code" value="<?php echo $filter_access_code ?>" /></td>
                <td><input type="text" class="date" name="filter_start_date" value="<?php echo $filter_start_date ?>" /></td>
                <td><input type="text" class="date" name="filter_end_date" value="<?php echo $filter_end_date ?>" /></td>
                <td valign="middle"><a class="button" id="trigger_filter" href="#">Filter</a></td>
            </tr>
            <?php foreach($table['data'] as $course){ ?>
            <tr>
                <td><input type="checkbox" class="selected" name="selected[]" value="<?php echo $course->id ?>" /></td>
                <td><?php echo $course->name ?></td>
                <td><?php echo $course->access_code ?></td>
                <td><?php echo $course->start_date ?></td>
                <td><?php echo $course->end_date ?></td>
                <td>
                    <p><?php echo Html::anchor('/course/summary/id/'.$course->id, 'View')?> /
                    <?php echo Html::anchor('/course/edit/id/'.$course->id, 'Edit')?></p>
                </td>
            </tr>
            <?php }?>
            <tr class="pagination">
                <td class="tar pagination" colspan="6">
                    <?php echo $pagination ?>
                </td>
            </tr>
        </table>
        </form>
    </div><!-- content -->
    
    <div class="clear"></div>
