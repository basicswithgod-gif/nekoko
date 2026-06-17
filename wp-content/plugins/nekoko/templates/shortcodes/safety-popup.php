<?php
/**
 * Expects: $kontakt_url
 */
defined( 'ABSPATH' ) || exit;
?>
<div id="nekoko-safety-overlay" role="dialog" aria-modal="true">
	<div id="nekoko-safety-modal">
		<div class="modal-header">
			<span class="modal-icon">&#9888;&#65039;</span>
			<h2>Važno obaveštenje o bezbednosti</h2>
		</div>
		<div class="modal-body">
			<p>NekoKo.rs je platforma koja spaja korisnike sa nezavisnim pružaocima usluga. <strong>NekoKo nije poslodavac niti agent pružalaca usluga</strong> i ne snosi odgovornost za njihove usluge.</p>
			<ul>
				<li>NekoKo <strong>ne procesira plaćanja</strong> — sve finansijske transakcije su direktno između tebe i pružaoca.</li>
				<li>Pre angažmana pružaoca, <strong>proveri reference</strong> i komuniciraj jasno.</li>
				<li>Zabranjene su ilegalne, nebezbedne, diskriminatorne i obmanjujuće usluge.</li>
				<li>Ako uočiš sumnjivo ponašanje, <a href="<?php echo esc_url( $kontakt_url ); ?>">prijavi nam</a>.</li>
			</ul>
		</div>
		<label class="modal-checkbox">
			<input type="checkbox" id="nekoko-safety-agree">
			<span>Razumem i prihvatam uslove korišćenja platforme NekoKo.rs.</span>
		</label>
		<button id="nekoko-safety-confirm" class="nekoko-btn" disabled style="width:100%;opacity:.5;cursor:not-allowed;">Razumem, nastavi</button>
	</div>
</div>
