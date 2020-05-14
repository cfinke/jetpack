/**
 * External dependencies
 */
import config from 'config';
/**
 * Internal dependencies
 */
import { step } from '../lib/setup-env';
import {
	doInPlaceConnection,
	syncJetpackPlanData,
	loginToWpSite,
	loginToWpcomIfNeeded,
} from '../lib/flows/jetpack-connect';
import {
	execWpCommand,
	getNgrokSiteUrl,
	resetWordpressInstall,
	execSyncShellCommand,
} from '../lib/utils-helper';
import Sidebar from '../lib/pages/wp-admin/sidebar';
import JetpackPage from '../lib/pages/wp-admin/jetpack';
import CheckoutPage from '../lib/pages/wpcom/checkout';
import ThankYouPage from '../lib/pages/wpcom/thank-you';
import MyPlanPage from '../lib/pages/wpcom/my-plan';
import PlansPage from '../lib/pages/wp-admin/plans';
const cookie = config.get( 'storeSandboxCookieValue' );
const cardCredentials = config.get( 'testCardCredentials' );

describe( 'Connection', () => {
	beforeEach( async () => {
		await resetWordpressInstall();
		await execWpCommand( 'wp config set JETPACK_SHOULD_USE_CONNECTION_IFRAME true' );
		await execWpCommand( 'wp plugin deactivate e2e-plan-data-interceptor' );

		await execSyncShellCommand(
			'rm /home/travis/wordpress/wp-content/plugins/e2e-plan-data-interceptor.php'
		);
		await loginToWpcomIfNeeded( 'defaultUser' );
		await loginToWpSite();
	} );

	afterAll( async () => {
		await resetWordpressInstall();
	} );

	it( 'In-place with Free plan', async () => {
		await step( 'Can start in-place connection', async () => {
			await ( await Sidebar.init( page ) ).selectJetpack();
			await doInPlaceConnection();
		} );

		await step( 'Can assert that site is connected', async () => {
			const jetpackPage = await JetpackPage.init( page );
			expect( await jetpackPage.isConnected() ).toBeTruthy();
		} );
	} );

	it( 'In-place upgrading a plan from personal to premium', async () => {
		await step( 'Can set a sandbox cookie', async () => {
			const siteUrl = getNgrokSiteUrl();
			const host = '.' + new URL( siteUrl ).host;
			const sidebar = await Sidebar.init( page );
			await sidebar.setSandboxModeForPayments( cookie );
			await sidebar.setSandboxModeForPayments( cookie, host );
		} );

		await step( 'Can start in-place connection', async () => {
			await ( await Sidebar.init( page ) ).selectJetpack();
			await doInPlaceConnection( 'personal' );
		} );

		await step( 'Can process payment for Personal plan', async () => {
			await ( await CheckoutPage.init( page ) ).processPurchase( cardCredentials );
			await ( await ThankYouPage.init( page ) ).waitForSetupAndProceed();
			await ( await MyPlanPage.init( page ) ).returnToWPAdmin();
			await syncJetpackPlanData( 'personal', false );
		} );

		await step( 'Can assert that site has a Personal plan', async () => {
			const jetpackPage = await JetpackPage.init( page );
			expect( await jetpackPage.isPlan( 'personal' ) ).toBeTruthy();
		} );

		await step( 'Can visit plans page and select a Premium plan', async () => {
			const jetpackPage = await JetpackPage.init( page );

			await jetpackPage.reload();
			await page.waitFor( 10000 );

			await jetpackPage.reload();
			await page.waitFor( 10000 );

			await jetpackPage.reload();
			await page.waitFor( 10000 );

			await jetpackPage.openPlans();
			const plansPage = await PlansPage.init( page );
			await plansPage.select( 'premium' );
		} );

		await step( 'Can process payment for Premium plan', async () => {
			await ( await CheckoutPage.init( page ) ).processPurchase( cardCredentials );
			await ( await ThankYouPage.init( page ) ).waitForSetupAndProceed();
			await ( await MyPlanPage.init( page ) ).returnToWPAdmin();
			await syncJetpackPlanData( 'premium', false );
		} );

		await step( 'Can assert that site has a Premium plan', async () => {
			const jetpackPage = await JetpackPage.init( page );
			expect( await jetpackPage.isPlan( 'premium' ) ).toBeTruthy();
		} );
	} );
} );
