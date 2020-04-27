/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, BlockControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl, Toolbar, ToolbarButton, Path } from '@wordpress/components';

/**
 * Internal dependencies
 */
import renderMaterialIcon from '../../../shared/render-material-icon';

const JetpackFieldControls = ( { setAttributes, required } ) => {
	return (
		<>
			<BlockControls>
				<Toolbar>
					<ToolbarButton
						title={ __( 'Required' ) }
						icon={ renderMaterialIcon(
							<Path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm4.24 16L12 15.45 7.77 18l1.12-4.81-3.73-3.23 4.92-.42L12 5l1.92 4.53 4.92.42-3.73 3.23L16.23 18z" />
						) }
						onClick={ () => {
							setAttributes( { required: ! required } );
						} }
						className={ required ? 'is-pressed' : undefined }
					/>
				</Toolbar>
			</BlockControls>

			<InspectorControls>
				<PanelBody title={ __( 'Field Settings', 'jetpack' ) }>
					<ToggleControl
						label={ __( 'Field is required', 'jetpack' ) }
						className="jetpack-field-label__required"
						checked={ required }
						onChange={ value => setAttributes( { required: value } ) }
						help={ __(
							'Does this field have to be completed for the form to be submitted?',
							'jetpack'
						) }
					/>
				</PanelBody>
			</InspectorControls>
		</>
	);
};

export default JetpackFieldControls;
