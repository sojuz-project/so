<?php 
if (!empty($pdata['media_imgs']) || !empty($pdata['download_imgs'])): 
    if ($node_num <= $total_node): ?>   
        <table class="wpsi-table wpsi-pagination table-fixheader">
        <?php if ($total_node > 1) : ?>
         	<thead>
                <tr><td> 
                        <button type="button" class="wpsi-image-preview-pagination button-previous 
                        <?php echo $node_num == 1 ? "button-disabled":''; ?>" data-val="<?php echo $node_num - 1 ?>" <?php echo $node_num == 1 ? "disabled" : ''; ?> > 	
                        	<span class="dashicons dashicons-arrow-left-alt2"></span> 
                       	</button> 
                    </td>
                    <td>
                        <input type="number" name="current" class="node_num" min="1" max="<?php echo $total_node; ?>" value="<?php echo $node_num; ?>" id="image_node_num" /> &nbsp; / &nbsp;
                       <span class="total-element"> <?php echo $total_node; ?> </span>   
                    </td>
                     <td> 
                        <button type="button" class="wpsi-image-preview-pagination button-next 
                        <?php echo $node_num == $total_node  ? "button-disabled":''; ?>"" data-val="<?php echo $node_num + 1 ?>" <?php echo $node_num == $total_node ? "disabled" : ''; ?> >
                            <span class="dashicons dashicons-arrow-right-alt2"></span> 
                        </button>
                    </td>
                </tr>
         	</thead>
        <?php endif; ?>
         	<tbody>
                <tr>
                    <td colspan="3" style="text-align: left;" class="row-data">
                    	<h2 class="block-title"> <?php wpsi_helper::_e("Image Name or URL"); ?> </h2>
        				<?php if (is_array($file_names)) : ?>
        						<div class="wpsi-name-block">
        						<?php foreach ($file_names as $idx => $src) {
        								$i = $idx + 1 ;
                                        echo "<p><strong>";
                                        wpsi_helper::_e("image-".$i." : ");
                                        echo "</strong>";
                                        if(!empty(trim($src)))
                                            echo '<a href="'. esc_url($src) .'" target="_blank">' . esc_html($src) . '</a>';
                                        echo  "</p>";
        							}
        						?>
        						</div>
                        <?php endif; ?>
        				<?php if (is_array($srcs)): ?>
            					<h2 class="block-title"><?php wpsi_helper::_e("Image Preview"); ?></h2>
        						<div class="wpsi-img-thumb">
        						<?php foreach ($srcs as  $src) :
        								echo "<img src='". esc_url($src) ."'>";
        							endforeach; ?>
        						</div> 
        				<?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php endif; 
endif; ?>