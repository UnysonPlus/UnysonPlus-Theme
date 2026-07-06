/**
 * Starter Page Templates generator.
 *
 * Composes the four bundled starter page layouts (Landing / About / Pricing /
 * Contact) as page-builder atom trees, writing one `<slug>.json` per template
 * into `up-templates/starter-pages/`. The theme registers those files with the
 * builder via the `fw_ext_builder:predefined_templates:page-builder:full`
 * filter (see inc/includes/starter-page-templates.php), so they appear in the
 * builder's Templates → Full Templates tab as read-only starters.
 *
 * Every atom is cloned from a VERIFIED full-att template harvested from the
 * live marketing pages (_src/att-templates.json) so the saved shapes are always
 * schema-correct; we only override content + a few layout atts. Run:
 *
 *     node up-templates/starter-pages/_src/build.mjs
 *
 * unique_id values are deterministic (md5 of a per-atom seed) so re-runs are
 * byte-stable and diffs stay clean.
 */
import { createHash } from 'node:crypto';
import { readFileSync, writeFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join } from 'node:path';

const HERE = dirname( fileURLToPath( import.meta.url ) );
const OUT_DIR = join( HERE, '..' );
const TPL = JSON.parse( readFileSync( join( HERE, 'att-templates.json' ), 'utf8' ) );

/* ---- helpers --------------------------------------------------------- */
const clone = ( o ) => JSON.parse( JSON.stringify( o ) );

let seedN = 0;
let seedPage = '';
const uid = () => createHash( 'md5' ).update( `${seedPage}:${seedN++}` ).digest( 'hex' );

/** Deep-merge src over a clone of dst (objects merge, scalars/arrays replace). */
function merge( dst, src ) {
	const out = clone( dst );
	for ( const k of Object.keys( src ) ) {
		const v = src[ k ];
		if ( v && typeof v === 'object' && ! Array.isArray( v ) && out[ k ] && typeof out[ k ] === 'object' && ! Array.isArray( out[ k ] ) ) {
			out[ k ] = merge( out[ k ], v );
		} else {
			out[ k ] = clone( v );
		}
	}
	return out;
}

/** A leaf shortcode atom with content/layout overrides. */
function atom( shortcode, overrides = {} ) {
	const base = TPL.atoms[ shortcode ];
	if ( ! base ) { throw new Error( `no att template for shortcode "${shortcode}"` ); }
	const atts = merge( base, overrides );
	atts.unique_id = uid();
	atts.css_id = '';
	atts.css_class = '';
	return { type: 'simple', shortcode, _items: [], atts };
}

/** A column wrapping `items`, with a width fraction + att overrides. */
function column( width, items, overrides = {} ) {
	const atts = merge( TPL.column, overrides );
	atts.unique_id = uid();
	atts.w_desktop = 'default';
	return { type: 'column', width, _items: items, atts };
}

/** A section wrapping `items`, with att overrides (backgrounds reset). */
function section( items, overrides = {} ) {
	const atts = merge( TPL.section, overrides );
	atts.unique_id = uid();
	atts.css_id = '';
	atts.css_class = '';
	return { type: 'section', _items: items, atts };
}

/* Convenience content builders -------------------------------------------------- */
const heading = ( o ) => atom( 'special_heading', o );
const text = ( html, o = {} ) => atom( 'text_block', { text: html, ...o } );
const btn = ( label, link, style = 'btn-primary', o = {} ) =>
	atom( 'button', { label, link, style, size: 'btn-lg', icon: { type: 'none' }, ...o } );

/** A centred icon_box "card". `svg` is the inner path markup for the badge. */
function iconCard( title, content, svgPath ) {
	const badge = `<span class="ti-ic" style="display:inline-grid;place-items:center;width:54px;height:54px;border-radius:12px;background:rgba(0,0,0,.06);margin-bottom:1rem;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="1.4em" height="1.4em">${svgPath}</svg></span>`;
	return atom( 'icon_box', {
		icon: { type: 'none' },
		custom_icon: badge,
		title,
		title_tag: 'h4',
		content: `<p>${content}</p>`,
		style: 'top-title',
		icon_align: 'center',
		title_align: 'center',
		content_align: 'center',
	} );
}

/** A stat: big animated counter + caption, centred. */
function stat( number, suffix, caption ) {
	return column( '1_4', [
		atom( 'counter', { number: String( number ), suffix, alignment: 'center', separator: 'yes' } ),
		text( `<p>${caption}</p>` ),
	], { content_h: 'center' } );
}

/* SVG path snippets (feather-style, stroke) */
const SVG = {
	zap: '<path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>',
	layers: '<path d="M12 2l10 6-10 6L2 8l10-6zM2 16l10 6 10-6"/>',
	shield: '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
	code: '<path d="M16 18l6-6-6-6M8 6l-6 6 6 6"/>',
	pen: '<path d="M12 20h9M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4 12.5-12.5z"/>',
	globe: '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 010 20 15 15 0 010-20z"/>',
	mail: '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 6l-10 7L2 6"/>',
	phone: '<path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3 19.5 19.5 0 01-6-6 19.8 19.8 0 01-3-8.6A2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.4 1.8.7 2.7a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.4-1.2a2 2 0 012.1-.5c.9.3 1.8.6 2.7.7a2 2 0 011.7 2z"/>',
	pin: '<path d="M21 10c0 7-9 12-9 12s-9-5-9-12a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>',
	heart: '<path d="M20.8 4.6a5.5 5.5 0 00-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 00-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 000-7.8z"/>',
	target: '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>',
	check: '<path d="M20 6L9 17l-5-5"/>',
};

/* A simple centred heading block (used to introduce sections). */
const sectionHead = ( overline, title, subtitle = '' ) =>
	column( '2_3', [
		heading( { overline, title, subtitle, heading: 'h2', alignment: 'center', overline_uppercase: 'yes' } ),
	], { content_h: 'center', offset_desktop: 'none' } );

/* ===================================================================== *
 * TEMPLATE DEFINITIONS
 * ===================================================================== */
const TEMPLATES = {};

/* ---- Landing --------------------------------------------------------- */
TEMPLATES.landing = {
	title: 'Starter — Landing Page',
	build: () => [
		section( [
			column( '2_3', [
				heading( {
					overline: 'Welcome',
					title: 'A headline that says exactly what you do.',
					subtitle: 'One clear sentence of supporting copy that explains the value and invites the visitor to act.',
					heading: 'h1',
					alignment: 'center',
				} ),
				btn( 'Get started', '#', 'btn-primary' ),
				btn( 'Learn more', '#', 'btn-outline' ),
			], { content_h: 'center' } ),
		], { content_valign: 'center' } ),

		section( [
			sectionHead( 'Features', 'Everything you need, nothing you don’t' ),
		] ),
		section( [
			column( '1_3', [ iconCard( 'Fast', 'Describe the first benefit in a sentence or two.', SVG.zap ) ], { content_h: 'center' } ),
			column( '1_3', [ iconCard( 'Flexible', 'Describe the second benefit in a sentence or two.', SVG.layers ) ], { content_h: 'center' } ),
			column( '1_3', [ iconCard( 'Secure', 'Describe the third benefit in a sentence or two.', SVG.shield ) ], { content_h: 'center' } ),
		] ),

		section( [
			stat( 12, 'k+', 'Happy customers' ),
			stat( 99, '%', 'Uptime' ),
			stat( 24, '/7', 'Support' ),
			stat( 40, '+', 'Integrations' ),
		] ),

		section( [
			column( '2_3', [
				heading( { overline: '', title: 'Ready to get started?', subtitle: 'A short closing line that removes the last bit of hesitation.', heading: 'h2', alignment: 'center' } ),
				btn( 'Start now', '#', 'btn-primary' ),
			], { content_h: 'center' } ),
		], { content_valign: 'center' } ),
	],
};

/* ---- About ----------------------------------------------------------- */
TEMPLATES.about = {
	title: 'Starter — About Page',
	build: () => [
		section( [
			column( '2_3', [
				heading( { overline: 'About us', title: 'We’re here to help you do your best work.', heading: 'h1', alignment: 'left' } ),
				text( '<p>Introduce your company in a paragraph. Who you are, what you make, and why it matters to the people you serve.</p>' ),
			] ),
		] ),

		section( [
			column( '1_2', [
				heading( { overline: 'Our story', title: 'How it started', heading: 'h2', alignment: 'left' } ),
				text( '<p>Tell the origin story. What problem did you notice, and what made you decide to solve it differently?</p><p>Keep it human and specific — a few honest sentences beat a wall of mission-speak.</p>' ),
			] ),
			column( '1_2', [
				heading( { overline: 'Our mission', title: 'Where we’re going', heading: 'h2', alignment: 'left' } ),
				text( '<p>Describe what you’re working toward and the principles that guide the way you build and support it.</p>' ),
			] ),
		] ),

		section( [ sectionHead( 'Values', 'What we care about' ) ] ),
		section( [
			column( '1_3', [ iconCard( 'Craft', 'We sweat the details others skip.', SVG.pen ) ], { content_h: 'center' } ),
			column( '1_3', [ iconCard( 'Openness', 'We work in the open and share what we learn.', SVG.globe ) ], { content_h: 'center' } ),
			column( '1_3', [ iconCard( 'People first', 'Real support from people who build the product.', SVG.heart ) ], { content_h: 'center' } ),
		] ),

		section( [
			stat( 2015, '', 'Founded' ),
			stat( 45, '', 'Team members' ),
			stat( 30, '+', 'Countries' ),
			stat( 1, 'M+', 'Users served' ),
		] ),

		section( [
			column( '2_3', [
				heading( { title: 'Want to work with us?', heading: 'h2', alignment: 'center' } ),
				btn( 'Get in touch', '#', 'btn-primary' ),
			], { content_h: 'center' } ),
		], { content_valign: 'center' } ),
	],
};

/* ---- Pricing --------------------------------------------------------- */
function plan( name, price, per, features, cta, featured ) {
	const items = [
		heading( { overline: name, title: price, subtitle: per, heading: 'h3', alignment: 'center' } ),
		text( `<ul>${features.map( ( f ) => `<li>${f}</li>` ).join( '' )}</ul>` ),
		btn( cta, '#', featured ? 'btn-primary' : 'btn-outline' ),
	];
	return column( '1_3', items, { content_h: 'center' } );
}

TEMPLATES.pricing = {
	title: 'Starter — Pricing Page',
	build: () => [
		section( [
			column( '2_3', [
				heading( { overline: 'Pricing', title: 'Simple, honest pricing', subtitle: 'Pick the plan that fits. Change or cancel any time.', heading: 'h1', alignment: 'center' } ),
			], { content_h: 'center' } ),
		], { content_valign: 'center' } ),

		section( [
			plan( 'Starter', '$0', 'per month', [ 'Core features', '1 project', 'Community support' ], 'Start free', false ),
			plan( 'Pro', '$19', 'per month', [ 'Everything in Starter', 'Unlimited projects', 'Priority support', 'Advanced analytics' ], 'Choose Pro', true ),
			plan( 'Team', '$49', 'per month', [ 'Everything in Pro', 'Team roles & SSO', 'Onboarding & SLA' ], 'Choose Team', false ),
		] ),

		section( [ sectionHead( 'FAQ', 'Questions, answered' ) ] ),
		section( [
			column( '1_2', [
				heading( { title: 'Can I change plans later?', heading: 'h4', alignment: 'left' } ),
				text( '<p>Yes — upgrade or downgrade at any time and we’ll prorate the difference.</p>' ),
			] ),
			column( '1_2', [
				heading( { title: 'Do you offer refunds?', heading: 'h4', alignment: 'left' } ),
				text( '<p>Describe your refund window and how to request one.</p>' ),
			] ),
		] ),

		section( [
			column( '2_3', [
				heading( { title: 'Still deciding?', subtitle: 'Talk to us and we’ll help you find the right fit.', heading: 'h2', alignment: 'center' } ),
				btn( 'Contact sales', '#', 'btn-primary' ),
			], { content_h: 'center' } ),
		], { content_valign: 'center' } ),
	],
};

/* ---- Contact --------------------------------------------------------- */
TEMPLATES.contact = {
	title: 'Starter — Contact Page',
	build: () => [
		section( [
			column( '2_3', [
				heading( { overline: 'Contact', title: 'Let’s talk', subtitle: 'We usually reply within one business day.', heading: 'h1', alignment: 'center' } ),
			], { content_h: 'center' } ),
		], { content_valign: 'center' } ),

		section( [
			column( '1_3', [ iconCard( 'Email', 'hello@example.com', SVG.mail ) ], { content_h: 'center' } ),
			column( '1_3', [ iconCard( 'Phone', '+1 (555) 000-0000', SVG.phone ) ], { content_h: 'center' } ),
			column( '1_3', [ iconCard( 'Visit', '123 Main St, Your City', SVG.pin ) ], { content_h: 'center' } ),
		] ),

		section( [
			column( '1_2', [
				heading( { overline: 'Get in touch', title: 'Send us a message', heading: 'h2', alignment: 'left' } ),
				text( '<p>Tell people what to expect when they reach out, and any details that help you respond faster.</p>' ),
			] ),
			column( '1_2', [
				text( '<p><strong>Add your form here.</strong> Drop in the Newsletter element or your contact-form shortcode to collect messages.</p>' ),
				btn( 'Email us', 'mailto:hello@example.com', 'btn-primary' ),
			] ),
		] ),
	],
};

/* ===================================================================== */
const manifest = [];
for ( const [ slug, def ] of Object.entries( TEMPLATES ) ) {
	seedPage = slug;
	seedN = 0;
	const tree = def.build();
	writeFileSync( join( OUT_DIR, `${slug}.json` ), JSON.stringify( tree ), 'utf8' );
	manifest.push( { slug, title: def.title } );
	console.log( `wrote ${slug}.json (${JSON.stringify( tree ).length} bytes)` );
}
writeFileSync( join( OUT_DIR, 'manifest.json' ), JSON.stringify( manifest, null, 2 ), 'utf8' );
console.log( 'wrote manifest.json:', manifest.map( ( m ) => m.slug ).join( ', ' ) );
