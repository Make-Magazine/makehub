<div class="csmm-tile" id="custom">
    <div class="csmm-tile-body">
        <div class="csmm-tile-title">Custom Code Settings</div>
        <p>Please double-check any custom code you enter in the settings below. Any typos or mistakes will affect the appearance of the page.</p>

        <div class="csmm-section-content">

            <div class="csmm-form-group">
                <label for="signals_csmm_css" class="csmm-strong">Custom CSS Code</label>
                <div id="signals_csmm_css_editor"></div>
                <textarea name="signals_csmm_css" id="signals_csmm_css" class="Signals_csmm_Block" rows="5" placeholder=""><?php echo stripslashes($signals_csmm_options['custom_css']); ?></textarea>

                <p class="csmm-form-help-block">Write only the CSS code. Do not include the &lt;style&gt; tags. Code is placed in the page's &lt;head&gt; tag.</p>
            </div>

            <div class="csmm-form-group">
                <label for="custom_head_code" class="csmm-strong">Custom Head Code</label>
                <div id="signals_csmm_custom_head_code"></div>
                <textarea name="custom_head_code" id="custom_head_code" rows="5"><?php echo stripslashes($signals_csmm_options['custom_head_code']); ?></textarea>

                <p class="csmm-form-help-block">The code will be outputted before the closing &lt;head&gt; tag. Make sure you include &lt;style&gt;, &lt;script&gt;, or any other tags as the code is outputted as-is, without being wrapped in any tags.</p>
            </div>

            <div class="csmm-form-group">
                <label for="custom_foot_code" class="csmm-strong">Custom Footer Code</label>
                <div id="signals_csmm_custom_foot_code"></div>
                <textarea name="custom_foot_code" id="custom_foot_code" rows="5"><?php echo stripslashes($signals_csmm_options['custom_foot_code']); ?></textarea>
                <p class="csmm-form-help-block">The code will be outputted before the closing &lt;body&gt; tag. Make sure you include &lt;style&gt;, &lt;script&gt;, or any other tags as the code is outputted as-is, without being wrapped in any tags.</p>
            </div>

        </div>
    </div>
</div><!-- #advanced -->