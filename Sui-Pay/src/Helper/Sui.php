<?php

namespace SuiPay\Helper;

/**
 * Method to fetch Sui transaction
 *
 * @param $digest
 * @return object
 */
function fetch_sui_transaction_block($digest, $network = "testnet")
{
  $url = 'https://sui-' . $network . '.mystenlabs.com/graphql';

  $headers = array(
    'Content-Type' => 'application/json'
  );
  $body = json_encode(array(
    'query' => '
            query getTx($digest: String!) {
                transactionBlock(digest: $digest) {
                    sender {
                      address
                    }
                    effects {
                        status
                        timestamp
                        balanceChanges {
                          nodes {
                            owner {
                              address
                            }
                            coinType {
                              repr
                              signature
                            }
                            amount
                          }
                        }
                    }
                }
            }
        ',
    'variables' => array(
      'digest' => $digest
    )
  ));
  $response = wp_remote_post($url, array(
    'headers' => $headers,
    'body' => $body,
    'method' => 'POST',
    'data_format' => 'body'
  ));

  if (is_wp_error($response)) {
    return array('error' => $response->get_error_message());
  }

  $response_body = wp_remote_retrieve_body($response);
  return json_decode($response_body, true);
}
