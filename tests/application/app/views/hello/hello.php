<?php
/*
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/	?>
<h1>hello world</h1>
<?php if ( $this->Request->ServerIsLocal()) { ?>
<table class="table">
	<thead>
		<tr>
			<td>&nbsp;</td>
			<td>IP</td>
			<td>Subnet</td>
			<td class="text-center">Local</td>
		</tr>

	</thead>
	<tbody>
		<tr>
			<td>Client</td>
			<td><?php
				print $ip = $this->Request->getRemoteIP();
			?></td>
			<td><?= $this->Request->getSubNet( $ip); ?></td>
			<td class="text-center"><?= $this->Request->ClientIsLocal( $ip) ? strings::html_tick : '&nbsp;'; ?></td>

		</tr>

		<tr>
			<td>Server</td>
			<td><?php
				print $ip = $this->Request->getServerIP();

				?></td>
			<td><?= $this->Request->getSubNet( $ip); ?></td>
			<td class="text-center"><?= $this->Request->ServerIsLocal( $ip) ? strings::html_tick : '&nbsp;'; ?></td>

		</tr>

	</tbody>

</table>

<?php } // if ( Request::ServerIsLocal()) ?>
