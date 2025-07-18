<?php
/**
 * The template for displaying PDF Invoice
 * Override by copying it to yourtheme/memberpress/account/invoice/modern.php.
 */

$color = isset($invoice->color) && !empty($invoice->color) ? $invoice->color : '#3993d1';
?>
<!DOCTYPE html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <style lang="text/css">
    @page {
      margin: 0
    }

    a {
      color: #5d6975;
      text-decoration: underline
    }

    h2,h3,h4,h5{
      margin: 0;
      color: #5d6975
    }

    p{
      margin: 0;
    }

    body {
      position: relative;
      margin: 0 auto;
      color: #001028;
      background: #fff;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
      font-size: 14px;
      font-weight: 400;
    }

    header {
      /* margin-bottom: 30px; */
      /* text-align: center; */
      border-top: 7px solid <?php echo $color ?>;
      border-bottom:1px solid <?php echo $color ?>
    }

    .container{
      width: 90%;
      margin: 0 auto;
      padding-top: 2em;
      padding-bottom: 2em;
      display: table;

    }

    table {
      width: 100%;
      border-collapse: collapse;
      border-spacing: 0;
      margin-bottom: 20px
    }

    table th{color:#5d6975;font-weight:400}

    table#desc td {
      vertical-align: top
    }

    table#desc tr td:last-child{
      text-align: right;
    }

    table#content th{
      text-align: left;
      font-weight: bold;
    }

    table#content td{
      background: #e2e6e8;
      border: 2px solid #fff;
      padding: 1em;
    }

    table#content td.grand{
      background: #fff;
      font-size: 1.3rem;
      border-bottom: 2px solid <?php echo $color ?>;
    }

    td.grand.total{
      color: <?php echo $color ?>
    }

    h1#header{
      background: <?php echo $color ?>;
      color: #fff;
      padding: 0.5rem;
      text-align: center;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    #logo{
      width: 7em;
    }

    .green-label{
      color: green;
      font-weight: bold;
    }

    footer{
      border-top: 3px solid <?php echo $color ?>;
      background:<?php echo MePdfInvoicesHelper::hex2rgba($color, 0.15) ?>;
      width:100%;
      position:absolute;
      bottom:0;
      left:0;
      right:0;
      padding:2em 0;
      text-align:center;
      }

      footer p{margin:0}
  </style>
</head>

<body>

  <header class="clearfix">
    <div class="container">
      <div id="logo">
        <?php if(is_numeric($invoice->logo)){ ?>
        <img src="<?php echo get_attached_file( $invoice->logo ); ?>">
        <?php } ?>
      </div>
      <div class="company"><?php echo wpautop( $invoice->company ); ?></div>
    </div>

  </header>
  <main>

  <div class="container">
    <table id="desc">
      <tr>
        <td>
          <p class="green-label"><?php esc_html_e( 'Bill To:', 'memberpress-pdf-invoice' ) ?></p>
          <div><?php echo wpautop( $invoice->bill_to ); ?></div>
        </td>
        <td>
        </td>
        <td>
        <?php if(isset($txn->order_id) && $txn->order_id > 0) : ?>
          <?php printf( '<p><span>%s:</span> %s</p>', esc_html__( 'Order NO', 'memberpress-pdf-invoice' ), strtoupper( $txn->order_id ) ); ?>
        <?php endif; ?>
        <?php if(absint($invoice->credit_number) > 0) : ?>
          <?php printf( '<p><span class="green-label">%s:</span> %s</p>', esc_html__( 'CREDIT NOTE NO', 'memberpress-pdf-invoice' ), strtoupper( $invoice->credit_number ) ); ?>
          <?php printf( '<p><span class="green-label">%s:</span> %s</p>', esc_html__( 'ORIG. Invoice NO', 'memberpress-pdf-invoice' ), strtoupper( $invoice->invoice_number ) ); ?>
        <?php else: ?>
          <?php printf( '<p><span class="green-label">%s:</span> %s</p>', esc_html__( 'Invoice NO', 'memberpress-pdf-invoice' ), strtoupper( $invoice->invoice_number ) ); ?>
        <?php endif; ?>
        <p><?php echo date_i18n( get_option( 'date_format' ), $invoice->paid_at ) ?></p>
        </td>
      </tr>
    </table>
    <?php if(absint($invoice->credit_number) > 0) : ?>
      <h1 id="header"><?php esc_html_e( 'Credit Note', 'memberpress-pdf-invoice' ) ?></h1>
    <?php else: ?>
      <h1 id="header"><?php esc_html_e( 'Invoice', 'memberpress-pdf-invoice' ) ?></h1>
    <?php endif; ?>
    <table id="content">
      <thead>
        <tr>
          <th><?php esc_html_e( 'DESCRIPTION', 'memberpress-pdf-invoice' ); ?></th>
          <?php if ( $invoice->show_quantity ) : ?>
            <th class="quantity"><?php esc_html_e( 'QUANTITY', 'memberpress-pdf-invoice' ); ?></th>;
          <?php endif; ?>
          <th><?php esc_html_e( 'AMOUNT', 'memberpress-pdf-invoice' ); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php

        foreach ( $invoice->items as $item ) {
          $item['quantity'] = 1;
          ?>
          <tr>
            <td><?php echo $item['description']; ?></td>
            <?php if ( $invoice->show_quantity ) : ?>
              <td class="unit"><?php echo MePdfInvoicesCtrl::format_real_number($item['quantity']); ?></td>;
            <?php endif; ?>
            <td class="mp-currency-cell"><?php echo MeprAppHelper::format_currency( $item['amount'], true, false ); ?></td>
          </tr>
          <?php
        }
        ?>

      <?php if ( isset( $invoice->coupon ) && ! empty( $invoice->coupon ) && $invoice->coupon['id'] != 0 ) : ?>
      <tr>
        <td><?php echo $invoice->coupon['desc']; ?></td>
          <?php if ( $invoice->show_quantity ) : ?>
        <td>&nbsp;</td>
        <?php endif; ?>
        <td class="mp-currency-cell">
          <?php if( $invoice->coupon['amount'] !== '0' ) : ?>
            -<?php echo MeprAppHelper::format_currency( $invoice->coupon['amount'], true, false ); ?>
          <?php else : ?>
            &nbsp;
          <?php endif; ?>
        </td>
      </tr>
      <?php endif; ?>

      <?php if( is_array( $invoice->tax_items ) && count( $invoice->tax_items ) ): ?>
      <tr>
        <td><?php esc_html_e( 'SUBTOTAL', 'memberpress-pdf-invoice' ); ?></td>
        <?php if ( $invoice->show_quantity ) : ?>
        <td>&nbsp;</td>
        <?php endif; ?>
        <td class="total"><?php echo MeprAppHelper::format_currency( $invoice->subtotal, true, false ); ?></td>
      </tr>
      <?php foreach( $invoice->tax_items as $tax_item ): ?>
          <?php if($tax_item['amount'] > 0 || $tax_item['percent'] > 0) : ?>
            <tr>
              <?php if( $invoice->show_quantity ): ?>
                <td>&nbsp;</td>
              <?php endif; ?>
              <td class="mepr-tax-invoice"><?php echo MeprUtils::format_tax_percent_for_display($tax_item['percent']) . '% ' . $tax_item['type']; ?> <?php if( count($invoice->tax_items) > 1 && isset($tax_item['post_title']) && ! empty($tax_item['post_title']) ):?><br /><small><?php echo esc_html($tax_item['post_title']); ?></small><?php endif; ?></td>
              <td class="mp-currency-cell"><?php echo MeprAppHelper::format_currency( $tax_item['amount'], true, false ); ?></td>
            </tr>
          <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>
        <tr>
          <td class="grand"><?php esc_html_e( 'GRAND TOTAL', 'memberpress-pdf-invoice' ); ?></td>
          <?php if ( $invoice->show_quantity ) : ?>
          <td class="grand">&nbsp;</td>
          <?php endif; ?>
          <td class="grand total"><?php echo MeprAppHelper::format_currency( $invoice->total, true, false ); ?></td>
        </tr>
      </tbody>
    </table>

  </div><!-- container -->

  <div class="container">
    <table id="notes">
      <tr>
        <td>
          <div class="notice"><?php echo wpautop( $invoice->notes ); ?></div>
        </td>
        <td>
          <img class="img-responsivel" style="width:150px; float:right" src="<?php echo esc_attr($invoice->paid_logo_url); ?>" alt="paid-stamp">
        </td>
      </tr>
    </table>

  </div>

  </main>
  <footer>
    <?php echo wpautop( $invoice->footnotes ); ?>
  </footer>
</body>

</html>
