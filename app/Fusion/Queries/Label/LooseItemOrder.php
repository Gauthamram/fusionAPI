<?php

namespace App\Fusion\Queries\Label;

use App\Fusion\Contracts\RawSqlInterface;

class LooseItemOrder implements RawSqlInterface
{
    public function query()
    {
        $this->sql = "SELECT item_master.item_parent AS style, ColourSEQ.Display_Seq AS colour_seq, SizeSEQ.Display_Seq AS size_seq, 'LooseItem' AS type, ordhead.order_no as order_number,
        	ordsku.item AS order_Item, item_master.item, '' AS pack_description,
        	item_master.short_desc AS description, Barcodes.Item AS barcode,
        	Barcodes.Item_Number_Type AS barcode_type,
        	ColourDiff.Diff_Desc AS colour, SizeDiff.DIFF_DESC AS item_size,
        	coalesce(stockroomlocator.UDA_TEXT, '0000') AS stockroomlocator, division.div_name as division_name,
        	coalesce(AUD.UNIT_RETAIL, -1) AS AUD,
            coalesce(NZD.Unit_Retail, -1) AS NZD, ordloc.qty_ordered as quantity,
            1 AS pack_quantity, ordsku.earliest_ship_date,
            coalesce(sup_attributes.Pre_Ticket_Ind, 'N') AS Pre_Ticket_Ind, 0 AS simplepackitemticketsreq,
            AUStore.Channel_Id
            FROM ordhead
            LEFT JOIN sup_attributes ON ordhead.supplier = sup_attributes.supplier
			INNER JOIN ordloc ON ordloc.order_no = ordhead.order_no
			INNER JOIN ordsku ON ordloc.order_no = ordsku.order_no AND OrdLoc.Item = ordsku.item
			INNER JOIN item_master ON item_master.item = ordsku.item AND item_master.pack_ind = 'N'
			INNER JOIN Item_Master Barcodes ON Item_Master.Item = Barcodes.Item_Parent
			AND Barcodes.Primary_Ref_Item_Ind = 'Y'
			INNER JOIN diff_ids SizeDiff ON item_master.diff_2 = SizeDiff.diff_id AND SizeDiff.diff_type = 'S'
			INNER JOIN diff_group_detail SizeSEQ ON SizeSEQ.diff_id = SizeDiff.diff_id
			AND SizeSEQ.diff_group_id = 'SIZ1'
			INNER JOIN diff_ids ColourDiff ON item_master.diff_1 = ColourDiff.diff_id AND ColourDiff.diff_type = 'C'
			INNER JOIN diff_group_detail ColourSEQ ON ColourSEQ.diff_id = ColourDiff.diff_id
			AND ColourSEQ.diff_group_id = 'CLR1'
			LEFT JOIN uda_item_ff stockroomlocator ON stockroomlocator.item = item_master.item
			AND stockroomlocator.uda_id = 900
			INNER JOIN deps ON item_master.dept = deps.dept
			INNER JOIN groups ON deps.group_no = groups.group_no
			INNER JOIN division ON Groups.Division = Division.Division
			INNER JOIN store AUStore ON ((AUStore.default_wh = ordloc.location AND ordloc.loc_type = 'W')
			OR (AUStore.store = ordloc.location AND ordloc.loc_type = 'S'))
			INNER JOIN CGL_RPT_CHANNEL_ZONE_MAP AUStoreZone ON AUStore.country_id = 'AU'
	        AND (AUStore.store = AUStoreZone.Primary_Store OR (AUStore.store in(1800,2800,20800)
	        and AUStoreZone.channel_id = AUStore.channel_id))
			INNER JOIN item_zone_price AUD ON AUD.zone_group_id = AUStoreZone.zone_group_id
			AND AUD.zone_id = AUStoreZone.zone_id AND AUD.item = item_master.item
			LEFT JOIN item_zone_price NZD ON NZD.ZONE_GROUP_ID = 1 AND NZD.zone_id = 4 AND NZD.ITEM = item_master.item
			WHERE ordhead.order_no = :order_no AND ordloc.qty_ordered > 0 and (ordhead.status = 'A' or ordhead.status = 'C')
			ORDER BY Style, Colour_SEQ, Size_SEQ";

        return $this;
    }

    public function filter($param = '')
    {
        return $this;
    }

    public function getSql()
    {
        return $this->sql;
    }
}
