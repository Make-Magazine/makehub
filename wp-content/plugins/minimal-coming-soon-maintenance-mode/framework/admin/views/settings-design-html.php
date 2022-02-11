<div class="csmm-tile" id="design-html">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Custom HTML Module</div>
        <p>If a module is missing, or you want full control over the page, this is the module to use. Make sure you double-check your custom HTML code.</p>

        <div class="csmm-section-content">

            <div class="csmm-form-group">
                <label for="signals_csmm_html" class="csmm-strong">Custom HTML</label>
                <div id="signals_csmm_html_editor"></div>
                <textarea name="signals_csmm_html" id="signals_csmm_html" rows="8" placeholder="Custom HTML for the plugin"><?php echo htmlentities(stripslashes($signals_csmm_options['custom_html'])); ?></textarea>

                <p class="csmm-form-help-block">The module is wrapped in CSS classes like all other modules when displayed: <i>.html-container</i> and <i>.mm-module</i>.</p>
            </div>

        </div>
    </div>
</div><!-- #html -->