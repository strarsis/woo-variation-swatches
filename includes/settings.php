<?php
	defined( 'ABSPATH' ) or die( 'Keep Quit' );
	
	/*	woo_variation_swatches()->add_setting( 'general', 'General', array(
			array(
				'title'  => 'Ticket Section',
				'desc'   => 'Ticket Settings',
				'fields' => array(
					array(
						'id'    => 'is_new_ticket_registration',
						'type'  => 'checkbox',
						'title' => 'New ticket with registration',
						'desc'  => 'Create new ticket with user registration',
						'value' => 1,
					),
					array(
						'id'    => 'user_can_make_private',
						'type'  => 'checkbox',
						'title' => 'User can make ticket private',
						'desc'  => 'User can make a choice for their ticket visibility.',
						'value' => 1,
					),
					array(
						'id'    => 'only_verified_client_can_replay',
						'type'  => 'checkbox',
						'title' => 'Verified client ticket replay',
						'desc'  => 'Only Verified Client Who created this ticket can replay his own ticket.',
						'value' => 1,
					),
					array(
						'id'    => 'non_ticket_author_replay_moderation',
						'type'  => 'checkbox',
						'title' => 'Non ticket author ticket moderation',
						'desc'  => 'Moderation replay when replay user is not ticket author.',
						'value' => 1,
					),
					array(
						'id'      => 'new_ticket_default_label',
						'type'    => 'select',
						'title'   => 'Default ticket label',
						'desc'    => 'Set a default ticket label',
						'options' => array( 'key1' => 'Value', 'key2' => 'Value 2' ),
					),
					array(
						'id'    => 'close_old_tickets_replay',
						'type'  => 'checkbox',
						'title' => 'Auto close replay',
						'desc'  => 'Automatically close replay for old tickets.',
						'value' => 1,
					
					),
					array(
						'id'      => 'close_old_tickets_replay_days',
						'type'    => 'number',
						'title'   => 'Auto close replay in',
						'desc'    => 'Automatically close replay on ticket older than nth day',
						'default' => '60',
						'min'     => 5,
						'max'     => 120,
						'step'    => 5,
						'suffix'  => 'days',
						'require' => array(
							'close_old_tickets_replay' => array(
								'type'  => 'equal',
								'value' => 1
							)
						)
					),
				)
			)
		), TRUE );*/