<?php

namespace App\Fusion\Queries\Label;

use App\Fusion\Contracts\RawSqlInterface;

class SimplePackOrder implements RawSqlInterface
{
    public function query()
    {
        $this->sql = "SELECT item_master.item_parent AS style, ColourSEQ.Display_Seq AS colour_seq,
    		SizeSEQ.Display_Seq AS size_seq, 'SimplePack' AS type, ordhead.order_no as order_number,
    		ordsku.item AS orderitem, item_master.item AS item, Pack.item_desc AS pack_description,
    		item_master.short_desc AS description, PackBarcodes.Item AS pack_barcode,
    		PackBarcodes.Item_Number_Type AS pack_barcode_type, Barcodes.Item AS barcode,
    		Barcodes.Item_Number_Type AS barcode_type, ColourDiff.Diff_Desc AS colour,
    		SizeDiff.DIFF_DESC AS item_size, coalesce(stockroomlocator.UDA_TEXT, 'PACK') AS stockroomlocator,
    		division.div_name as division_name, coalesce(AUD.UNIT_RETAIL, -1) AS aud,
    		coalesce(NZD.Unit_Retail, -1) AS nzd, ordloc.qty_ordered as quantity, Packitem.Pack_Qty as pack_quantity,
    		ordsku.earliest_ship_date, coalesce(sup_attributes.Pre_Ticket_Ind, 'N') AS pre_ticket_ind,
    		coalesce(
        CASE sup_traits_matrix.sup_trait WHEN 9008 THEN 1
          ELSE 0
        END, 0) AS simplepackitemticketsreq,
    		AUStore.Channel_Id
            FROM ordhead
          	LEFT JOIN sup_attributes ON ordhead.supplier = sup_attributes.supplier
          	LEFT JOIN sup_traits_matrix ON ordhead.supplier = sup_traits_matrix.supplier
          	AND sup_traits_matrix.sup_trait = 9008
          	INNER JOIN ordloc ON OrdHead.Order_No = ordloc.order_no
          	INNER JOIN ordsku ON ordhead.order_no = ordsku.order_no AND ordloc.item = ordsku.item
          	AND ordloc.qty_ordered > 0
          	INNER JOIN item_master Pack ON Pack.Item = ordloc.item AND Pack.pack_ind = 'Y'
          	AND Pack.Simple_Pack_Ind = 'Y'
          	INNER JOIN packitem ON ordloc.item = packitem.pack_no
          	INNER JOIN item_master ON item_master.item = packitem.item
          	INNER JOIN Item_Master PackBarcodes ON PackBarcodes.item_parent = ordsku.item
          	AND PackBarcodes.Primary_Ref_Item_Ind = 'Y'
          	INNER JOIN Item_Master Barcodes ON Barcodes.item_parent = packitem.item
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
          	AND AUD.zone_id = AUStoreZone.zone_id
            AND AUD.item = item_master.item
            LEFT JOIN item_zone_price NZD ON NZD.ZONE_GROUP_ID = 1 AND NZD.zone_id = 4 AND NZD.ITEM = item_master.item
          	WHERE ordhead.order_no = :order_no and (ordhead.status = 'A' or ordhead.status = 'C')
          	ORDER BY style, colour_seq, size_seq ";

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
