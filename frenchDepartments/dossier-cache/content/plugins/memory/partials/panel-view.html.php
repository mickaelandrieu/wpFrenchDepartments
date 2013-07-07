<div class="wrap theme-options-page">
	<div id="icon-options-general" class="icon32"></div>
	<h2>Panneau de sélection du mode de jeu</h2>
	<form action="" method="post">
		<div class="theme-options-group">
			<table cellspacing="0" class="widefat options-table">
				<thead>
					<tr>
						<th colspan="2">Selectionner le type de Memory</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td scope="row">
							<label for="option_memory">%config_option%</label>
						</td>
						<td>
							<select name="option_memory" id="options_memory">
								<optgroup label="Choisissez une option"> 
							  		<option value="1">Memory avec Images</option>
							  		<option value="2">Memory avec chiffres</option>
							  	</optgroup>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<p class="submit">
			<input type="submit" name="memory_update" class="button-primary autowidth" value="sauvegarder"/>
		</p>
	</form>
</div>