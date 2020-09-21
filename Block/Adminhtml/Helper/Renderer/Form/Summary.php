<?php
/**
 * Magiccart 
 * @category 	Magiccart 
 * @copyright 	Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license 	http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-03-04 11:44:03
 * @@Modify Date: 2018-07-13 17:19:42
 * @@Function:
 */
namespace Magiccart\Comments\Block\Adminhtml\Helper\Renderer\Form;

class Summary extends \Magento\Backend\Block\Template
{

    protected $_commentFactory;
    protected $_rating = null;

    public function __construct(
    	\Magento\Backend\Block\Template\Context $context,
		\Magiccart\Comments\Model\CommentFactory $commentFactory,
		array $data = []
    )
    {
        $this->_commentFactory = $commentFactory;
        parent::__construct($context, $data);
    }

    public function getRating()
    {
		if( $this->_rating != null ) return $this->_rating;
		$id = $this->getRequest()->getParam('comment_id');
		if($id){
			$storeViewId = $this->getRequest()->getParam('store');
			$model = $this->_commentFactory->create();
			$model->setStoreViewId($storeViewId)->load($id);
            if ($model->getId()) $this->_rating = $model->getData('rating_summary');
		}
		return $this->_rating;
    }

    public function ratingHtml()
    {	
		$rating = 0;
		if($this->getRating()) $rating = ceil($this->getRating() * 20);
		$html = '<div class="rating-box">';
		        	$html .= '<div class="rating" style="width:'. $rating .'%;"></div>';			
		$html .= '</div>';
		return $html;
    }

    public function detailedHtml()
    {
    	$rating = $this->getRating();
    	$html = '';
		$html .= '<div class="product-review-box">
			        <table cellspacing="0" id="product-review-table">
			            <thead>
			                <tr>';
			                	for ($i=1; $i<=5; $i++) {
			                		$html .= '<th><span class="nobr">'. __("$i star") .'</span></th>';
			                	}
		$html .=	   		'</tr>
			            </thead>
			            <tbody>
			                <tr class="odd last">';
			                	$class 		= 'class="first"';
			                	for ($i=1; $i<=5; $i++) {
			                		$checked	= ( $i == $rating) ? 'checked="checked"' : '';
			                    	$html .= '<td '.$class.' style="width:60px;" ><input style="display:block; text-align: center; margin: 0 auto;" type="radio" name="rating_summary" id="rating_' .$i.'" value="'.$i.'" '.$checked.' /></td>';
			                		$class = ($i == 4) ? 'class="last"' : '';
			                	}
		$html .=            '</tr>
			            </tbody>
			        </table>
			    </div>';
		return $html;
    }

}

