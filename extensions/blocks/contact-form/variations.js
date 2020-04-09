/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { Path } from '@wordpress/components';

/**
 * Internal dependencies
 */
import renderMaterialIcon from '../../shared/render-material-icon';

const variations = [
	{
		name: 'contact-form',
		title: __( 'Contact Form', 'jetpack' ),
		description: __( 'Add a contact form to your page.', 'jetpack' ),
		isDefault: true,
		icon: renderMaterialIcon(
			<Path d="M21.99 8c0-.72-.37-1.35-.94-1.7l-8.04-4.71c-.62-.37-1.4-.37-2.02 0L2.95 6.3C2.38 6.65 2 7.28 2 8v10c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2l-.01-10zm-11.05 4.34l-7.2-4.5 7.25-4.25c.62-.37 1.4-.37 2.02 0l7.25 4.25-7.2 4.5c-.65.4-1.47.4-2.12 0z" />,
			48,
			48,
			'-4 -4 32 32'
		),
		innerBlocks: [
			[ 'jetpack/field-name', { required: true } ],
			[ 'jetpack/field-email', { required: true } ],
			[ 'jetpack/field-textarea', {} ],
		],
		attributes: {
			submitButtonText: __( 'Contact Us', 'jetpack' ),
		},
	},
	{
		name: 'rsvp-form',
		title: __( 'RSVP Form', 'jetpack' ),
		description: __( 'Add an RSVP form to your page', 'jetpack' ),
		icon: renderMaterialIcon(
			<Path d="M10 9V7.41c0-.89-1.08-1.34-1.71-.71L3.7 11.29c-.39.39-.39 1.02 0 1.41l4.59 4.59c.63.63 1.71.19 1.71-.7V14.9c5 0 8.5 1.6 11 5.1-1-5-4-10-11-11z" />,
			48,
			48,
			'-4 -3 32 32'
		),
		innerBlocks: [
			[ 'jetpack/field-name', { required: true } ],
			[ 'jetpack/field-email', { required: true } ],
			[
				'jetpack/field-radio',
				{
					label: __( 'Attending?', 'jetpack' ),
					required: true,
					options: [ 'Yes', 'No' ],
				},
			],
			[ 'jetpack/field-textarea', { label: __( 'Other Details', 'jetpack' ) } ],
		],
		attributes: {
			submitButtonText: __( 'Send RSVP', 'jetpack' ),
			subject: __( 'A new RSVP from your website', 'jetpack' ),
		},
	},
	{
		name: 'registration-form',
		title: __( 'Registration Form', 'jetpack' ),
		description: __( 'Add a Registration form to your page', 'jetpack' ),
		icon: renderMaterialIcon(
			<Path d="M11.34 15.02c.39.39 1.02.39 1.41 0l6.36-6.36c.39-.39.39-1.02 0-1.41L14.16 2.3c-.38-.4-1.01-.4-1.4-.01L6.39 8.66c-.39.39-.39 1.02 0 1.41l4.95 4.95zm2.12-10.61L17 7.95l-4.95 4.95-3.54-3.54 4.95-4.95zm6.95 11l-2.12-2.12c-.18-.18-.44-.29-.7-.29h-.27l-2 2h1.91L19 17H5l1.78-2h2.05l-2-2h-.42c-.27 0-.52.11-.71.29l-2.12 2.12c-.37.38-.58.89-.58 1.42V20c0 1.1.9 2 2 2h14c1.1 0 2-.89 2-2v-3.17c0-.53-.21-1.04-.59-1.42z" />,
			48,
			48,
			'-4 -3 32 32'
		),
		innerBlocks: [
			[ 'jetpack/field-name', { required: true } ],
			[ 'jetpack/field-email', { required: true } ],
			[ 'jetpack/field-telephone', { label: __( 'Phone Number', 'jetpack' ) } ],
			[
				'jetpack/field-select',
				{
					label: __( 'How did you hear about us?', 'jetpack' ),
					options: [ 'Search Engine', 'Social Media', 'TV', 'Radio', 'Friend or Family' ],
				},
			],
			[ 'jetpack/field-textarea', { label: __( 'Other Details', 'jetpack' ) } ],
		],
		attributes: {
			submitButtonText: __( 'Send', 'jetpack' ),
			subject: __( 'A new registration from your website', 'jetpack' ),
		},
	},
	{
		name: 'appointment-form',
		title: __( 'Appointment Form', 'jetpack' ),
		description: __( 'Add an Appointment booking form to your page', 'jetpack' ),
		icon: renderMaterialIcon(
			<Path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V8c0-.55-.45-1-1-1s-1 .45-1 1v2H2c-.55 0-1 .45-1 1s.45 1 1 1h2v2c0 .55.45 1 1 1s1-.45 1-1v-2h2c.55 0 1-.45 1-1s-.45-1-1-1H6zm9 4c-2.67 0-8 1.34-8 4v1c0 .55.45 1 1 1h14c.55 0 1-.45 1-1v-1c0-2.66-5.33-4-8-4z" />,
			48,
			48,
			'-4 -3 32 32'
		),
		innerBlocks: [
			[ 'jetpack/field-name', { required: true } ],
			[ 'jetpack/field-email', { required: true } ],
			[ 'jetpack/field-telephone', { required: true } ],
			[ 'jetpack/field-date', { label: __( 'Date', 'jetpack' ), required: true } ],
			[
				'jetpack/field-radio',
				{
					label: __( 'Time', 'jetpack' ),
					required: true,
					options: [ 'Morning', 'Afternoon' ],
				},
			],
			[ 'jetpack/field-textarea', { label: __( 'Notes', 'jetpack' ) } ],
		],
		attributes: {
			submitButtonText: __( 'Book Appointment', 'jetpack' ),
			subject: __( 'A new appointment booked from your website', 'jetpack' ),
		},
	},
	{
		name: 'feedback-form',
		title: __( 'Feedback Form', 'jetpack' ),
		description: __( 'Add a Feedback form to your page', 'jetpack' ),
		icon: renderMaterialIcon(
			<Path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.03 0 3.8-1.11 4.75-2.75.19-.33-.05-.75-.44-.75H7.69c-.38 0-.63.42-.44.75.95 1.64 2.72 2.75 4.75 2.75z" />,
			48,
			48,
			'-4 -3 32 32'
		),
		innerBlocks: [
			[ 'jetpack/field-name', { required: true } ],
			[ 'jetpack/field-email', { required: true } ],
			[
				'jetpack/field-radio',
				{
					label: __( 'Please rate our website', 'jetpack' ),
					required: true,
					options: [ '1 - Very Bad', '2 - Poor', '3 - Average', '4 - Good', '5 - Excellent' ],
				},
			],
			[ 'jetpack/field-textarea', { label: __( 'How could we improve?', 'jetpack' ) } ],
		],
		attributes: {
			submitButtonText: __( 'Send Feedback', 'jetpack' ),
			subject: __( 'New feedback received from your website', 'jetpack' ),
		},
	},
];

export default variations;
