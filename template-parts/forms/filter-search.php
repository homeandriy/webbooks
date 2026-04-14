<form class="form-horizontal" id="main-search">
    <div class="row pd-15">
        <div class="col-xs-12 col-sm-12 mrg-t">
            <label for="category-main"><?php esc_html_e('Category', 'webbooks'); ?></label>
            <?php
            $select = wp_dropdown_categories('show_option_none=' . esc_html__('Category', 'webbooks') . '&orderby=slug&value_field=slug&echo=0&child_of=18');
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_dropdown_categories() builds safe HTML for the dropdown.
            echo preg_replace("#<select([^>]*)>#", "<select class='form-control' id='category-main' $1 >", $select);
            ?>
        </div>
        <div class="col-xs-12 col-sm-12"><label for="status-book"><?php esc_html_e('Select skill level', 'webbooks'); ?></label><select id="status-book" class="form-control choose-complexity" disabled="disabled"><option value=""><?php esc_html_e('Select', 'webbooks'); ?></option><?php foreach (\Domain\Book\Complexity::cases() as $complexity) : ?><option value="<?php echo esc_attr($complexity->value); ?>"><?php echo esc_html($complexity->label()); ?></option><?php endforeach; ?></select></div>
        <div class="col-xs-12 col-sm-12"><label for="language"><?php esc_html_e('Language', 'webbooks'); ?></label><select id="language" class="form-control choose-complexity" disabled="disabled"><option value=""><?php esc_html_e('Select', 'webbooks'); ?></option><?php foreach (\Domain\Book\Language::cases() as $language) : ?><option value="<?php echo esc_attr($language->value); ?>"><?php echo esc_html($language->label()); ?></option><?php endforeach; ?></select></div>
        <div class="col-xs-12 col-sm-12 mrg-b mrg-t"><button type="submit" id="send-data-button" class="btn btn-primary" disabled="disabled"><?php esc_html_e('Search', 'webbooks'); ?></button></div>
    </div>
</form>
