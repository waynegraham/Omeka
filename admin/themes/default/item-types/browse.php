<?php 
$pageTitle = __('Browse Item Types') . ' ' . __('(%s total)', $total_records);
head(array('title'=>$pageTitle,'bodyclass'=>'item-types')); ?>

<?php if (has_permission('ItemTypes', 'add')): ?>
<?php echo link_to('item-types', 'add', __('Add an Item Type'), array('class'=>'add green button')); ?>
<?php endif ?>

    <table class="full">
        <thead>
            <tr>
                <th><?php echo __('Type Name'); ?></th>
                <th><?php echo __('Description'); ?></th>
                <th><?php echo __('Total Items'); ?></th>
                <?php if (has_permission('ItemTypes', 'edit')): ?>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        
<?php while (loop_item_types()): ?>
<?php $itemtype = get_current_item_type();?>
<tr class="itemtype">
    <td class="itemtype-name">
        <a href="<?php echo html_escape(record_uri($itemtype, 'show', 'item-types')); ?>"><?php echo html_escape($itemtype->name); ?></a>
        <ul class="action-links group">
        <?php if (has_permission('ItemTypes', 'edit')): ?>
            <li><a class="edit" href="<?php echo html_escape(uri('item-types/edit/'.$itemtype->id)); ?>"><?php echo __('Edit'); ?></a></li>
        <?php endif; ?>        
        </ul>
    </td>
    <td class="itemtype-description"><?php echo html_escape($itemtype->description); ?></td>
    <td><?php echo link_to_items_with_item_type(); ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
<?php fire_plugin_hook('admin_append_to_item_types_browse_primary', array('item_types' => $itemtypes)); ?>
</div>
<?php foot(); ?>
