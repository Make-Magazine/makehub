<div class="sb-customizer-preview" :data-preview-device="customizerScreens.previewScreen">
	<?php
		/**
		 * CFF Admin Notices
		 *
		 * @since 4.0
		 */
		do_action('sbi_admin_notices');

		$feed_id = ! empty( $_GET['feed_id'] ) ? (int)$_GET['feed_id'] : 0;
	?>
	<div class="sb-preview-ctn sb-tr-2">
		<div class="sb-preview-top-chooser sbi-fb-fs">
			<strong :class="getModerationShoppableMode == true ? 'sbi-moderate-heading' :''" v-html="getModerationShoppableMode == false ? genericText.preview : ( svgIcons['eyePreview'] + '' + (customizerScreens.activeSection === 'settings_shoppable_feed' ? genericText.shoppableModePreview : genericText.moderationModePreview) )"></strong>
			<div class="sb-preview-chooser" v-if="getModerationShoppableMode == false">
				<button class="sb-preview-chooser-btn" v-for="device in previewScreens" v-bind:class="'sb-' + device" v-html="svgIcons[device]" @click.prevent.default="switchCustomizerPreviewDevice(device)" :data-active="customizerScreens.previewScreen == device"></button>
			</div>
		</div>

		<div class="sbi-preview-ctn sbi-fb-fs">
			<div>
				<component :is="{template}"></component>
			</div>
			<div class="sbi-moderation-pagination sbi-fb-fs" v-if="getModerationShoppableMode">
				<div v-if="getModerationShoppableModeOffset" class="sbi-moderation-pagination-btn sb-btn sb-btn-grey" @click.prevent.default="moderationModePagination('previous')">{{genericText.previous}}</div>
				<div class="sbi-moderation-pagination-btn sb-btn sb-btn-grey" @click.prevent.default="moderationModePagination('next')">{{genericText.next}}</div>
			</div>
			<?php
				include_once SBI_BUILDER_DIR . 'templates/preview/light-box.php';
			?>
		</div>

	</div>
	<sbi-dummy-lightbox-component :dummy-light-box-screen="dummyLightBoxScreen" :customizer-feed-data="customizerFeedData"></sbi-dummy-lightbox-component>

</div>


