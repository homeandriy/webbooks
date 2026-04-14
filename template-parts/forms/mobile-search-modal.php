<div class="modal fade" id="mobile-search-modal" tabindex="-1" role="dialog" aria-labelledby="mobileSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="mobileSearchModalLabel"><?php esc_html_e( 'Search', 'webbooks' ); ?></h4>
            </div>
            <div class="modal-body">
                <div class="input-group">
                    <input
                        type="text"
                        class="form-control trans input-lg main-search"
                        data-idres="search-result-mobile-modal"
                        placeholder="<?php esc_attr_e( 'Search...', 'webbooks' ); ?>"
                        autofocus
                    >
                    <div class="dropdown-menu mCustomScrollbar custom-search" data-mcs-theme="dark" id="search-result-mobile-modal-wrap">
                        <div class="search-loader" aria-hidden="true">
                            <i class="fa fa-spinner fa-spin"></i>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tbody id="search-result-mobile-modal"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
