<div class="sui-donate-box sui-wrap">
  <span>
    <?php echo $title; ?>
  </span>
  <form class="sui-donate-form">
    <div class="sui-form-field">
      <span class="sui-label">Amount:</span>
      <div class="sui-donate-amounts">
        <label>
          <input checked="checked" type="radio" class="sui-donate-amount" name="amount" value="10"> 10
        </label>
        <label>
          <input type="radio" class="sui-donate-amount" name="amount" value="25"> 25
        </label>
        <label>
          <input type="radio" class="sui-donate-amount" name="amount" value="100"> 100
        </label>
        <label>
          <input type="radio" class="sui-donate-amount" name="amount" value="other"> other
        </label>
      </div>
    </div>
    <div class="sui-form-field sui-donate-custom sui-hide">
      <label class="sui-label" for="customAmount">Custom Amount:</label>
      <input class="sui-input" type="number" name="custom" min="1" minlength="1" value="1">
    </div>
    <div class="sui-form-field">
      <label class="sui-label" for="currency">Select Currency:</label>
      <select class="sui-input" name="currency">
        <option value="SUI" selected="selected">SUI</option>
        <?php // <option value="BUCKS">BUCKS</option> 
        ?>
      </select>
    </div>
    <input type="hidden" name="receiver" value="<?php echo esc_attr($receiver); ?>">
    <div class="sui-donate-button">
      <button type="submit" class="sui-button sui-button-primary">
        <?php echo $button; ?>
      </button>
    </div>
  </form>
</div>
