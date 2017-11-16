<?php
// PAGINAÇÃO
// stats
$listStart	= $lim0 + 1;
$listEnd		= $lim0 + $num_rows;
if($found_rows != $num_rows) :
	$html .= '
		<div class="base-app-pagination float-left">
			'.$pageNav->getListFooter().'
			<div class="list-stats small text-muted mt-1">
				'.JText::sprintf('LIST_STATS', $listStart, $listEnd, $found_rows).'
			</div>
		</div>
	';
else :
	$html .= '
		<div class="base-app-pagination float-left text-muted">
			'.JText::sprintf('LIST_NUM_ROWS', $found_rows).'
		</div>
	';
endif;
?>
