/**
 * Functions
 */

function suiPayAmount({ amount, to, onSuccess, execute = true }) {
  if (!window.sui) {
    console.error(
      "If you are the website admin, please install the SUI-Login widget to use SUI-Pay",
    );
    return;
  }

  if (!window.sui.currentAccount) {
    window.sui.toggleModal(true);
    return;
  }

  const txb = new window.sui.createPayTx(amount, to);

  function handleSuccess(result) {
    if (onSuccess) {
      return onSuccess(result);
    }

    console.log("signed transaction block", result);
    alert("success");
  }

  if (execute) {
    return window.sui.signAndExecuteTransactionBlock(
      {
        transactionBlock: txb,
      },
      {
        onSuccess: handleSuccess,
      },
    );
  }

  return window.sui.signTransactionBlock(
    {
      transactionBlock: txb,
    },
    {
      onSuccess: handleSuccess,
    },
  );
}

/**
 * Subscription
 */
function onSuiPaywallSignSuccess(result) {
  console.log(result);
  jQuery.ajax({
    type: "POST",
    url: "/wp-admin/admin-ajax.php",
    data: {
      action: "sui_process_subscribe",
      digest: result.digest,
    },
    success: function (response) {
      console.log(response);
    },
    error: function (error) {
      console.log(error);
    },
  });
}

jQuery(document).ready(function () {
  jQuery(".sui-paywall-button").click(function () {
    const amount = jQuery(this).data("amount");
    const receiver = jQuery(this).data("receiver");

    suiPayAmount({
      amount,
      to: receiver,
      onSuccess: onSuiPaywallSignSuccess,
      execute: true,
    });
  });
});

/**
 * Donation Box
 */
jQuery(document).ready(function () {
  jQuery(".sui-donate-box").each(function () {
    var $donationBox = jQuery(this);
    var $customAmountField = $donationBox.find(".sui-donate-custom");

    $donationBox.find(".sui-donate-amount").on("change", function () {
      if (
        $donationBox
          .find('input[value="other"].sui-donate-amount')
          .is(":checked")
      ) {
        $customAmountField.removeClass("sui-hide");
      } else {
        $customAmountField.addClass("sui-hide");
      }
    });
  });

  jQuery(".sui-donate-form").submit(function (e) {
    e.preventDefault();

    var formData = {};

    var selectedAmount = jQuery(this)
      .find('input[name="amount"]:checked')
      .val();
    if (selectedAmount === "other") {
      selectedAmount = jQuery(this).find('input[name="custom"]').val();
    }
    formData.amount = selectedAmount || "1";

    formData.currency = jQuery(this).find('select[name="currency"]').val();

    // Get the receiver
    formData.receiver = jQuery(this).find('input[name="receiver"]').val();

    suiPayAmount(formData.amount, formData.receiver);
  });
});

/**
 * Pay Button
 */
jQuery(document).ready(function () {
  jQuery(".sui-pay-button").click(function () {
    const amount = jQuery(this).data("amount");
    const receiver = jQuery(this).data("receiver");
    suiPayAmount(amount, receiver);
  });
});
