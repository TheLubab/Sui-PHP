import { h, render } from "preact";
import { useEffect, useState } from "preact/hooks";

import {
  ConnectModal,
  WalletProvider,
  createNetworkConfig,
  SuiClientProvider,
  useCurrentAccount,
  useSignPersonalMessage,
  useSignTransactionBlock,
  useSignAndExecuteTransactionBlock,
  useDisconnectWallet,
} from "@mysten/dapp-kit";
import { getFullnodeUrl } from "@mysten/sui.js/client";

import "./theme.css";
import "@mysten/dapp-kit/dist/index.css";

import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { TransactionBlock } from "@mysten/sui.js/transactions";
import { MIST_PER_SUI } from "@mysten/sui.js/utils";
import BN from "bn.js";

const { networkConfig } = createNetworkConfig({
  testnet: { url: getFullnodeUrl("testnet") },
  mainnet: { url: getFullnodeUrl("mainnet") },
});

const queryClient = new QueryClient();

var sui = {
  currentAccount: undefined,
  toggleModal: undefined,
  disconnect: undefined,
  signPersonalMessage: undefined,
  verifyPersonalMessage: undefined,
  signTransactionBlock: undefined,
  signAndExecuteTransactionBlock: undefined,
  TransactionBlock,
  isConnected: window.sui_server?.account != "" && window.sui_server?.account,
};
window.sui = sui;

const SuiModal = () => {
  const [open, setOpen] = useState(false);
  const [signing, setSigning] = useState(false);

  const currentAccount = useCurrentAccount();
  const { mutate: disconnect } = useDisconnectWallet();
  const { mutate: signPersonalMessage } = useSignPersonalMessage();
  const { mutate: signTransactionBlock } = useSignTransactionBlock();
  const { mutate: signAndExecuteTransactionBlock } =
    useSignAndExecuteTransactionBlock();

  if (currentAccount) {
    console.log("connected", currentAccount);
  }

  sui.toggleModal = setOpen;
  sui.currentAccount = useCurrentAccount();
  sui.disconnect = disconnect;
  sui.signPersonalMessage = signPersonalMessage;
  sui.signTransactionBlock = signTransactionBlock;
  sui.signAndExecuteTransactionBlock = signAndExecuteTransactionBlock;

  sui.createPayTx = (amount, to) => {
    if (!to) return;

    const txb = new TransactionBlock();

    const parts = amount.toString().split(".");
    let multiplier = BigInt(Math.pow(10, 9));
    let bigAmount;
    if (parts.length > 1) {
      if (parts[0].length > 6) {
        // if big amount, ignore decimal
        bigAmount = BigInt(amount);
      } else {
        // fix it to 4 decimals then remove the comma
        bigAmount = BigInt(
          parseInt(parseFloat(amount).toFixed(4) * Math.pow(10, 4)),
        );
        // remove 4 zeros from the multiplier
        multiplier = BigInt(Math.pow(10, 5));
      }
    } else {
      bigAmount = BigInt(amount);
    }

    const realAmount = bigAmount * multiplier;

    const [coin] = txb.splitCoins(txb.gas, [realAmount.toString()]);
    txb.transferObjects([coin], to);

    return txb;
  };

  function onVerifyDone(result) {
    jQuery.ajax({
      type: "POST",
      url: "/wp-admin/admin-ajax.php",
      data: {
        action: "loginWithSui",
        signature: result.signature,
      },
      success: async (result) => {
        setSigning(false);
        if (result.error) {
          alert(result.error);
        } else {
          location.reload();
        }
      },
      error: (error) => {
        setSigning(false);
        console.log(error);
      },
    });
  }

  useEffect(() => {
    if (!currentAccount) return;

    if (!signing && !sui.isConnected) {
      setSigning(true);
      signPersonalMessage(
        {
          message: new TextEncoder().encode(window.sui_server.nonce),
        },
        {
          onSuccess: onVerifyDone,
        },
      );
    }
  }, [currentAccount]);

  return (
    <ConnectModal
      open={open}
      onOpenChange={(isOpen) => {
        if (!isOpen) {
          setOpen(false);
        }
      }}
    />
  );
};

const App = () => {
  const [activeNetwork, setActiveNetwork] = useState(
    window.sui_server.network || "testnet",
  );

  sui.network = activeNetwork;
  sui.setNetwork = setActiveNetwork;

  return (
    <QueryClientProvider client={queryClient}>
      <SuiClientProvider
        networks={networkConfig}
        network={activeNetwork}
        onNetworkChange={(network) => {
          setActiveNetwork(network);
        }}
      >
        <WalletProvider autoConnect={!!sui.isConnected}>
          <SuiModal />
        </WalletProvider>
      </SuiClientProvider>
    </QueryClientProvider>
  );
};

jQuery(document).ready(() => {
  jQuery(document.body).append("<div id='login-with-sui-modal'></div>");
  render(<App />, document.getElementById("login-with-sui-modal"));

  jQuery(".login-with-sui-link").click(async (event) => {
    event.preventDefault();
    sui.disconnect();
    sui.toggleModal(true);
  });

  jQuery(".logout-with-sui-link").click(async (event) => {
    event.preventDefault();
    sui.disconnect();
    window.location.href = event.currentTarget.href;
  });

  jQuery(".wp-admin-bar-logout > a").click(async (event) => {
    event.preventDefault();
    sui.disconnect();
    window.location.href = event.currentTarget.href;
  });
});
