<section class="wrapper">

<main id="content">

	<div class="breadcrumbs">
	<a href="<?php echo home_url(); ?>"><?php esc_attr_e('Home', 'onecommunity'); ?></a> / <span class="current"><?php the_title(); ?></span>
	</div>

	<h1 class="page-title"><?php esc_html_e( 'Create A New Group', 'buddypress' ); ?></h1>

<?php bp_nouveau_groups_create_hook( 'before', 'page' ); ?>

	<h2 class="bp-subhead"></h2>

	<?php bp_nouveau_groups_create_hook( 'before', 'content_template' ); ?>

	<?php if ( 'group-invites' !== bp_get_groups_current_create_step() ) : ?>
		<form action="<?php bp_group_creation_form_action(); ?>" method="post" id="create-group-form" class="standard-form" enctype="multipart/form-data">
	<?php else : ?>
		<div id="create-group-form" class="standard-form">
	<?php endif; ?>

		<?php bp_nouveau_groups_create_hook( 'before' ); ?>

		<?php bp_nouveau_template_notices(); ?>

		<div class="item-body" id="group-create-body">

			<nav class="<?php bp_nouveau_groups_create_steps_classes(); ?>" id="group-create-tabs" role="navigation" aria-label="<?php esc_attr_e( 'Group creation menu', 'buddypress' ); ?>">
				<ol class="group-create-buttons button-tabs">

					<?php bp_group_creation_tabs(); ?>

				</ol>
			</nav>

			<?php bp_nouveau_group_creation_screen(); ?>

		</div><!-- .item-body -->

		<?php bp_nouveau_groups_create_hook( 'after' ); ?>

	<?php if ( 'group-invites' !== bp_get_groups_current_create_step() ) : ?>
		</form><!-- #create-group-form -->
	<?php else : ?>
		</div><!-- #create-group-form -->
	<?php endif; ?>

	<?php bp_nouveau_groups_create_hook( 'after', 'content_template' ); ?>

<?php
bp_nouveau_groups_create_hook( 'after', 'page' ); ?>

</main><!-- .content -->

</section><!-- .wrapper -->
<?php get_footer(); ?>
