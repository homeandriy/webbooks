<form class="form-horizontal" id="main-search">
    <div class="row pd-15">
        <div class="col-xs-12 col-sm-12 mrg-t">
            <label for="category-main"><?php esc_html_e('Раздел', 'webbooks'); ?></label>
            <?php
            $select = wp_dropdown_categories('show_option_none=' . esc_html__('Раздел', 'webbooks') . '&orderby=slug&value_field=slug&echo=0&child_of=18');
            echo wp_kses_post(preg_replace("#<select([^>]*)>#", "<select class='form-control' id='category-main' $1 >", $select));
            ?>
            <input id='category-main' type="submit" class="btn btn-primary" value="<?php echo esc_attr__('Перейти в раздел', 'webbooks'); ?>">
        </div>
        <div class="col-xs-12 col-sm-12"><label for="status-book"><?php esc_html_e('Выберите скилл', 'webbooks'); ?></label><select id="status-book" class="form-control choose-complexity" disabled="disabled"><option><?php esc_html_e('Выбрать', 'webbooks'); ?></option><?php foreach (\Domain\Book\Complexity::cases() as $complexity) : ?><option value="<?php echo esc_attr($complexity->value); ?>"><?php echo esc_html($complexity->label()); ?></option><?php endforeach; ?></select></div>
        <div class="col-xs-12 col-sm-12"><label for="language"><?php esc_html_e('Язык', 'webbooks'); ?></label><select id="language" class="form-control choose-complexity" disabled="disabled"><option><?php esc_html_e('Выбрать', 'webbooks'); ?></option><?php foreach (\Domain\Book\Language::cases() as $language) : ?><option value="<?php echo esc_attr($language->value); ?>"><?php echo esc_html($language->label()); ?></option><?php endforeach; ?></select></div>
        <div class="col-xs-12 col-sm-12 mrg-b mrg-t"><button type="submit" id="send-data-button" class="btn btn-primary" disabled="disabled"><?php esc_html_e('Поиск', 'webbooks'); ?></button></div>
    </div>
</form>
