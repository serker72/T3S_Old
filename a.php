<?php
	function test($p) {
		?>
		<table border="1" bgcolor="#c3c3c3">
		<tr>
		<td>Test</td>
		<td><?php echo $p; ?></td>
		</tr>
		</table>
		<?php
	}

	$a = test('Tru-la-la');
	echo $a;
?>