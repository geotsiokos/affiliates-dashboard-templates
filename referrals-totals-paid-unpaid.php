<?php
/**
 * referrals.php
 *
 * Copyright (c) 2010 - 2019 "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author Karim Rahimpur
 * @package affiliates
 * @since affiliates 4.0.0
 *
 * This is a template file. You can customize it by copying it
 * into the appropriate subfolder of your theme:
 *
 *   mytheme/affiliates/dashboard/referrals.php
 *
 * It is highly recommended to use a child theme for such customizations.
 * Child themes are suitable to keep things up-to-date when the parent
 * theme is updated, while any customizations in the child theme are kept.
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @var Affiliates_Dashboard_Referrals $section Section object available for use in the template.
 */
?>
<h2><?php esc_html_e( 'Referrals', 'affiliates' ); ?></h2>
<?php
	//
	// Render the referrals filter form
	//
?>
<div class="dashboard-section dashboard-section-referrals">
	<form id="setfilters" class="filters capsule-container" action="" method="post">
		<div class="capsule half left">
			<label for="from_date" class="from-date-filter"><?php _e( 'From', 'affiliates' ); ?></label>
			<input class="datefield from-date-filter" name="from_date" type="date" value="<?php echo esc_attr( $section->get_from_date() ); ?>"/>
		</div>
		<div class="capsule half right">
			<label for="thru_date" class="thru-date-filter"><?php esc_html_e( 'Until', 'affiliates' ); ?></label>
			<input class="datefield thru-date-filter" name="thru_date" type="date" class="datefield" value="<?php echo esc_attr( $section->get_thru_date() ); ?>"/>
		</div>
		<div class="capsule full">
			<label for="search" class="search-filter"><?php esc_html_e( 'Search', 'affiliates' ); ?></label>
			<input class="input-text search-filter" title="<?php echo esc_attr( __( 'Search for a term in referrals', 'affiliates' ) ); ?>" name="search" type="text" value="<?php echo esc_attr( stripslashes( $section->get_search() ) ); ?>" placeholder="<?php esc_html_e( 'Search for &hellip;', 'affiliates' ); ?>"/>
		</div>
		<div class="filter-buttons">
			<input class="button apply-button" type="submit" name="apply_filters" value="<?php esc_html_e( 'Apply', 'affiliates' ); ?>"/>
			<input class="button clear-button" type="submit" name="clear_filters" value="<?php esc_html_e( 'Clear', 'affiliates' ); ?>"/>
		</div>
	</form>
	<?php
		//
		// Filter styles
		//
	?>
	<style type="text/css">
	.dashboard-section-referrals form.filters {
		background-color: #f2f2f2;
		border-radius: 4px;
		margin: 4px;
		padding: 4px;
	}
	.dashboard-section-referrals .capsule-container {
		width: 100%;
		display: grid;
		grid-template-columns: repeat(auto-fill, 25%);
	}
	.dashboard-section-referrals .capsule-container .capsule.half.left {
		grid-column: 1 / 3;
	}
	.dashboard-section-referrals .capsule-container .capsule.half.right {
		grid-column: 3 / 5;
	}
	.dashboard-section-referrals .capsule-container .capsule.full {
		grid-column: 1 / 5;
	}
	.dashboard-section-referrals .capsule-container .capsule {
		display: flex;
		padding: 4px;
		margin: 4px;
		align-items: center;
	}
	.dashboard-section-referrals .capsule-container .capsule label {
		padding: 0 4px;
	}
	.dashboard-section-referrals .capsule-container .capsule input {
		flex: 1;
		overflow: hidden;
	}
	.dashboard-section-referrals .filters .filter-buttons {
		display: flex;
		flex-wrap: wrap;
		margin: 4px;
		grid-column: 1 / 5;
	}
	.dashboard-section-referrals .filters .filter-buttons input {
		flex-shrink: 1;
		margin: 4px;
	}
	</style>
<?php
	//
	// Render the referrals section
	//
?>
<div class="referrals-container">
	<?php $primary_columns = 0; ?>
	<?php foreach ( $section->get_columns() as $key => $column ) : ?>
		<?php
		$primary_columns++;
		$order_options = array(
			'orderby' => $key,
			'order' => $section->get_switch_sort_order()
		);
		$class = '';
		$arrow = '';
		if ( strcmp( $key, $section->get_orderby() ) == 0 ) {
			$lorder = strtolower( $section->get_sort_order() );
			$class = "$key manage-column sorted $lorder";
			switch( $lorder ) {
				case 'asc' :
					$arrow = ' &uarr;';
					break;
				case 'desc' :
					$arrow = ' &darr;';
					break;
			}
		} else {
			$class = "$key manage-column sortable";
		}
		$link = $section->get_url( $order_options );
		?>
		<div class="cell heading <?php echo esc_attr( $class ); ?>">
			<?php if ( $key !== 'items' ) : ?>
			<a href="<?php echo esc_url( $link ); ?>" title="<?php echo esc_html( $column['description'] ); ?>">
				<span><?php echo esc_html( $column['title'] ); ?></span><span class="sorting-indicator"><?php echo $arrow; ?></span>
			</a>
			<?php else : ?>
				<span><?php echo esc_html( $column['title'] ); ?></span>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
	<?php // Render the entries ?>
	<?php if ( $section->get_count() > 0 ) :
		$i = 0;
		$unpaid_sum = array();
		$paid_sum = array();
		foreach ( $section->get_entries() as $entry ) :
			switch( $entry->status ) {
				case AFFILIATES_REFERRAL_STATUS_ACCEPTED :
					$unpaid_sum[$entry->currency_id] += $entry->amount;
					break;
				case AFFILIATES_REFERRAL_STATUS_CLOSED :
					$paid_sum[$entry->currency_id] += $entry->amount;
					break;
				default :
					break;
			}
			//echo '<p> Unpaid Sum ' . $unpaid_sum . '</p>';
			Affiliates_Templates::include_template(
				'dashboard/referrals-entry.php',
				array(
					'section' => $section,
					'entry'   => $entry,
					'index'   => $i
				)
			);
			
			$i++;
		endforeach;
	?>

	<?php else : ?>
		<div class="cell odd full"><?php esc_html_e( 'There are no results.', 'affiliates' ); ?></div>
	<?php endif; ?>
	

</div><?php // .referrals-container ?>
<?php
	//
	// Render the section navigation
	//
?>
<?php if ( $section->get_count() > 0 ) : ?>
	<div class="section-navigation">
		<?php if ( $section->get_current_page() > 0 ) : ?>
			<a style="margin: 4px;" class="button" href="<?php echo esc_url( $section->get_url( array( 'referrals-page' => $section->get_current_page() - 1 ) ) ); ?>"><?php echo esc_html_x( 'Previous', 'Label used to show previous page of affiliate referral results', 'affiliates' ); ?></a>
		<?php endif; ?>
		<?php if ( $section->get_current_page() < $section->get_pages() - 1 ) : ?>
			<a style="margin: 4px;" class="button" href="<?php echo esc_url( $section->get_url( array( 'referrals-page' => $section->get_current_page() + 1 ) ) ); ?>"><?php echo esc_html_x( 'Next', 'Label used to show next page of affiliate referral results', 'affiliates' ); ?></a>
		<?php endif; ?>
	</div>
	<div class="section-navigation-options">
		<form action="<?php echo esc_url( $section->get_url( array( 'per_page' => null ) ) ); ?>" method="post">
			<label class="row-count">
				<?php esc_html_e( 'Results per page', 'affiliates' ); ?>
				<input class="per-page" name="per_page" type="text" value="<?php echo esc_attr( $section->get_per_page() ); ?>" placeholder="<?php echo esc_attr( $section::PER_PAGE_DEFAULT ); ?>" />
				<input class="button" type="submit" value="<?php esc_attr_e( 'Apply', 'affiliates' ); ?>"/>
			</label>
		</form>
	</div>
<?php endif; ?>
</div><?php // .dashboard-section-referrals ?>

<h3><?php echo esc_html( 'Totals' ); ?></h3>
<div class="dashboard-section dashboard-section-referrals">
	<div class="referrals-container">
		<div class="cell odd"><strong>Total unpaid amount</strong>
		<p>
		<?php
		if ( count( $unpaid_sum ) > 0 ) :
			foreach ( $unpaid_sum as $currency_id => $sum ) :
				echo '<div>' . esc_html( $currency_id ) . ': ' . esc_html( $sum ) . '</div>';
			endforeach;
		else :
			echo '<div style="text-align:center;">0</div>';
		endif;
		?>
		</p>
		</div>
		<div class="cell even"><strong>Total paid amount</strong>
		<p>
		<?php
			if ( count( $paid_sum ) > 0 ) :
				foreach ( $paid_sum as $currency_id => $sum ) :
					echo '<div>' . esc_html( $currency_id ) . ': ' . esc_html( $sum ) . '</div>';
				endforeach;
			else :
				echo '<div style="text-align:center;">0</div>';
			endif;
		?>
		</p>
		</div>
	</div>
</div>

<?php
	//
	// Section styles
	//
?>
<style type="text/css">
.dashboard-section-referrals .referrals-container {
	width: 100%;
	display: grid;
	grid-template-columns: 20% 20% 20% 40%;
	margin: 4px;
}
.dashboard-section-referrals .referrals-container .cell {
	word-break: break-word;
	padding: 4px;
	background-color: #f0f0f0;
	padding: 4px;
}
.dashboard-section-referrals .referrals-container .cell.full {
	grid-column: 1 / -1;
}
.dashboard-section-referrals .referrals-container .date {
	grid-column: 1 / 2;
	word-break: break-word;
	font-size: larger;
}
.dashboard-section-referrals .referrals-container .amount {
	grid-column: 2 / 3;
	font-size: larger;
}
.dashboard-section-referrals .referrals-container .amount:not(.heading) {
	text-align: right;
}
.dashboard-section-referrals .referrals-container .status {
	grid-column: 3 / 4;
	font-size: larger;
}
.dashboard-section-referrals .referrals-container .items {
	grid-column: 4 / 5;
	font-size: larger;
}
.dashboard-section-referrals .referrals-container .data {
	grid-column: 5 / 6;
	font-size: larger;
}
.dashboard-section-referrals .referrals-container .heading {
	background-color: #ffffff;
	color: 171717;
	font-weight: bold;
	word-break: break-word;
	border-bottom: 4px solid #9e9e9e;
}
.dashboard-section-referrals .referrals-container .odd {
	background-color: #ffffff;
	color: #252525;
}
.dashboard-section-referrals .referrals-container .even {
	background-color: #e0e0e0;
	color: #171717;
}
.dashboard-section-referrals .section-navigation-options {
	margin: 4px;
}
.dashboard-section-referrals .section-navigation-options input.per-page {
	width: 4em;
}
@media only screen and (max-width: 768px) {
	.dashboard-section-referrals .referrals-container .heading {
		border: none;
	}
	.dashboard-section-referrals .referrals-container div.cell:nth-child(4) {
		border-bottom: 4px solid #9e9e9e;
	}
	.dashboard-section-referrals .referrals-container {
		grid-template-columns: 100%;
	}
	.dashboard-section-referrals .referrals-container .date {
		grid-column: 1;
		word-break: break-word;
	}
	.dashboard-section-referrals .referrals-container .amount {
		grid-column: 1;
	}
	.dashboard-section-referrals .referrals-container .amount:not(.heading) {
		text-align: initial;
	}
	.dashboard-section-referrals .referrals-container .status {
		grid-column: 1;
	}
	.dashboard-section-referrals .referrals-container .items {
		grid-column: 1;
	}
	.dashboard-section-referrals .referrals-container .data {
		grid-column: 1;
	}
	.dashboard-section-referrals .referrals-container .heading {
		font-size: small;
	}
	.dashboard-section-referrals .referrals-container .cell::before {
		display: block;
		font-size: smaller;
		font-weight: bolder;
		content: attr(data-heading);
	}
}
</style>
<?php
	//
	// Referral item styles
	//
?>
<style type="text/css">
	.dashboard-section-referrals .referral-item {
		width: 100%;
		display: grid;
		grid-template-columns: 75% 25%;
		margin: 4px;
	}
	.dashboard-section-referrals .referral-item-title {
		grid-column: 1;
	}
	.dashboard-section-referrals .referral-item-amount {
		word-break: break-word;
		grid-column: 2;
	}
</style>
