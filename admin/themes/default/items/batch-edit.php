<?php
$title = 'Batch Edit Items';
if (!$isPartial):
    head(array('title' => $title, 
               'bodyclass' => 'advanced-search', 
               'bodyid' => 'advanced-search-page'));
?>
<h1><?php echo $title; ?></h1>
<div id="primary">
    <script type="text/javascript">
        jQuery(window).load(function(){
            var otherFormElements = jQuery('#batch-edit-form select, #batch-edit-form input[type="text"]');
            jQuery('input[name=delete]').change(function() {
                if (this.checked) {
                    otherFormElements.attr('disabled', 'disabled');
                } else {
                    otherFormElements.removeAttr('disabled');
                }
            });
        });
    </script>
<?php endif; ?>
<form id="batch-edit-form" action="<?php echo html_escape(uri('items/batch-edit-save')); ?>" method="post" accept-charset="utf-8">
    <fieldset id="item-list" style="float:right; width: 28%;">
        <legend>Items</legend>
        <p><em>Changes will be applied to checked items.</em></p>
        <div style="height: 250px; overflow-y: auto; overflow-x:hidden;border: 1px solid #ddd; padding: 10px;">
        <?php 
        $itemCheckboxes = array();
        foreach ($itemIds as $id) {
            $itemCheckboxes[$id] = item('Dublin Core', 'Title', null, get_item_by_id($id));
        }
        echo $this->formMultiCheckbox('items[]', null, array('checked' => 'checked'), $itemCheckboxes); ?>
        </div>
    </fieldset>
    
    <fieldset id="item-fields" style="width: 70%; margin-bottom:2em;">
        <legend>Item Metadata</legend>
        <div class="field">
        <label for="metadata[public]">Public?</label>
        <?php
        $publicOptions = array(''  => 'Select Below',
                               '1' => 'Public',
                               '0' => 'Not Public'
                               );
        echo $this->formSelect('metadata[public]', null, array(), $publicOptions); ?>
        </div>
        <div class="field">
        <label for="metadata[featured]">Featured?</label>
        <?php
        $featuredOptions = array(''  => 'Select Below',
                                 '1' => 'Featured',
                                 '0' => 'Not Featured'
                                 );
        echo $this->formSelect('metadata[featured]', null, array(), $featuredOptions); ?>
        </div>
        
        <div class="field">
        <label for="metadata[item_type_id]">Item Type</label>
        <?php
        $itemTypeOptions = get_db()->getTable('ItemType')->findPairsForSelectForm();
        $itemTypeOptions = array('' => 'Select Below ', 'null' => 'Remove Item Type') + $itemTypeOptions;
        echo $this->formSelect('metadata[item_type_id]', null, array(), $itemTypeOptions);
        ?>
        </div>
        
        <div class="field">
        <label for="metadata[collection_id]">Collection</label>
        <?php
        $collectionOptions = get_db()->getTable('Collection')->findPairsForSelectForm();
        $collectionOptions = array('' => 'Select Below ', 'null' => 'Remove from Collection') + $collectionOptions;
        echo $this->formSelect('metadata[collection_id]', null, array(), $collectionOptions);
        ?>
        </div>

        <div class="field">
            <label for="metadata[tags]">Add Tags</label>
            <?php echo $this->formText('metadata[tags]', null, array('size' => 32, 'class' => 'textinput')); ?>
            <p class="explanation">Comma-separated list of tags to add to all checked items.</p>
        </div>
    </fieldset>
    <fieldset style="width: 70%;">
        <legend>Delete Items</legend>
        <p class="explanation">Check if you wish to delete selected items.</p>
        <div class="field">
            <label for="delete">Delete</label>
            <?php echo $this->formCheckbox('delete'); ?>
        </div>
    </fieldset>
    <input type="submit" value="Save Changes">
</form>
<?php if (!$isPartial): ?>
</div>
<?php foot(); ?>
<?php endif; ?>