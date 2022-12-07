<?php

/**
 * The file that defines a simple client for use with graphql.
 *
 * @link       https://www.activecampaign.com/
 *
 * @package    Activecampaign_For_Woocommerce
 * @subpackage Activecampaign_For_Woocommerce/includes/api-client
 */
class Activecampaign_For_Woocommerce_Api_Client_Graphql extends Activecampaign_For_Woocommerce_Api_Client {


	/**
	 *
	 * @param string                                                                                            $operation operation.
	 * @param string|null                                                                                       $first_key firstKey.
	 * @param Activecampaign_For_Woocommerce_Api_Serializable|Activecampaign_For_Woocommerce_Api_Serializable[] $body_objects objects in body.
	 * @param array                                                                                             $response_fields fields you want in response.
	 *
	 * @return string
	 */
	public function mutation( $operation, $first_key, $body_objects, $response_fields = array() ) {
		try {
			$params = Activecampaign_For_Woocommerce_Simple_Graphql_Serializer::graphql_serialize( $first_key, $body_objects );
		} catch ( Throwable $t ) {
			$this->logger->error(
				'There was an issue serializing the data for graphql.',
				[
					'first_key'    => $first_key,
					'body_objects' => $body_objects,
					'message'      => $t->getMessage(),
					'trace'        => $t->getTrace(),
				]
			);
		}

		try {
			$body_objects = 'mutation{' . $operation . '(' . $params . '){' . implode( ' ', $response_fields ) . '}}';
		} catch ( Throwable $t ) {
			$this->logger->error(
				'Failed to form Graphql mutation. No response.',
				[
					'message'         => $t->getMessage(),
					'response_fields' => $response_fields,
					'operation'       => $operation,
					'params'          => $params,
					'trace'           => $t->getTrace(),
				]
			);
		}

		$this->logger->debug( 'Body objects sent to AC', array( $body_objects ) );
		if ( $body_objects ) {
			$response = $this->post( '' )
					 ->with_body( $body_objects )
					->execute(
						array(
							'content-type'      => 'application/graphql',
							'wc-plugin-version' => ACTIVECAMPAIGN_FOR_WOOCOMMERCE_VERSION,
						)
					);
		}

		if ( ! $response ) {
			$this->logger->error(
				'Failed Graphql call. No response.',
				[
					'body' => $body_objects,
				]
			);
			throw new RuntimeException( 'Failed Graphql call. No response.' );
		}

		if (
			is_array( $response ) &&
			200 !== $response['code'] &&
			201 !== $response['code']
		) {

			$this->logger->error(
				'Failed Graphql call. No response.',
				array(
					'body'    => $body_objects,
					'message' => isset( $response['message'] ) ? $response['message'] : null,
					'code'    => isset( $response['code'] ) ? $response['code'] : null,
				)
			);

			throw new RuntimeException( 'Failed Graphql call. Error response. ' . $response['message'], $response['code'] );
		}

		if ( method_exists( $response, 'getBody' ) ) {
			$this->logger->info( "Made graphQL API call to $operation response: " . $response->getStatusCode() . $response->getReasonPhrase() );

			$response_body  = $response->getBody();
			$response_array = json_decode( $response_body, true );

			if ( $response_array['errors'] && count( $response_array['errors'] ) > 0 ) {
				global $activecampaign_for_woocommerce_product_sync_status;
				$activecampaign_for_woocommerce_product_sync_status[] = "Body: $body_objects";
				$this->logger->error(
					'Graphql returned errors:',
					[
						'response_array' => $response_array,
						'response_body'  => $response_body,
					]
				);
				throw new RuntimeException( "Failed Graphql call. Response: $response_body" );
			}

			return (string) $response_body;
		} else {
			return $response;
		}
	}

	/**
	 *
	 * @param string $operation operation.
	 * @param string $integration_name integration name.
	 * @param string $connection_unique_identifier unique identifier, usually the URL set up on the connection for woocommerce.
	 * @param array  $response_fields fields you want in response.
	 *
	 * @return string
	 */
	public function sync_mutation( $operation, $integration_name, $connection_unique_identifier, $response_fields = array() ) {
		$body_objects = 'mutation{' . $operation . '(integrationName:"' . $integration_name . '" connectionUniqueIdentifier:"' . $connection_unique_identifier . '"){' . implode( ' ', $response_fields ) . '}}';

		$this->logger->debug( 'Body objects', array( $body_objects ) );

		$response = $this->post( '' )
						 ->with_body( $body_objects )
						->execute(
							array(
								'content-type'      => 'application/graphql',
								'wc-plugin-version' => ACTIVECAMPAIGN_FOR_WOOCOMMERCE_VERSION,
							)
						);

		if ( ! $response ) {
			$this->logger->error(
				'Failed Graphql call. No response.',
				[
					'body' => $body_objects,
				]
			);
			throw new RuntimeException( 'Failed Graphql call. No response.' );
		}

		if (
			is_array( $response ) &&
			200 !== $response['code'] &&
			201 !== $response['code']
		) {

			$this->logger->error(
				'Failed Graphql call. No response.',
				array(
					'body'    => $body_objects,
					'message' => isset( $response['message'] ) ? $response['message'] : null,
					'code'    => isset( $response['code'] ) ? $response['code'] : null,
				)
			);

			throw new RuntimeException( 'Failed Graphql call. Error response. ' );
		}

		if ( method_exists( $response, 'getBody' ) ) {
			$this->logger->info(
				"Made graphQL API call to $operation response: ",
				[
					'code'   => $response->getStatusCode(),
					'reason' => $response->getReasonPhrase(),
				]
			);

			$response_body  = $response->getBody();
			$response_array = json_decode( $response_body, true );

			if ( $response_array['errors'] && count( $response_array['errors'] ) > 0 ) {
				global $activecampaign_for_woocommerce_product_sync_status;
				$activecampaign_for_woocommerce_product_sync_status[] = "Body: $body_objects";
				$this->logger->error(
					'Graphql returned errors:',
					[
						'response_array' => $response_array,
						'response_body'  => $response_body,
					]
				);

				throw new RuntimeException( "Failed Graphql call. Response: $response_body" );
			}

			return (string) $response_body;
		} else {
			return $response;
		}
	}

	public function operation( $operation, $body, $response_fields = array() ) {
		$body = '{' . $operation . '(' . $body . ')}{' . explode( ' ', $response_fields ) . '}}';
		$this->post( '' )
			 ->with_body( $body );
	}
}
