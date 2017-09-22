<?php

namespace App\Fusion\Commands;

use Config;
use App\User;

class Sql
{
    protected $user;
    /**
     * [__construct]
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    /**
     * Get Sql QUery based on the string
     */
    public function GetSql($string, $status, $type = 'Tickets')
    {
        switch ($string) {
        case 'AllOrders':
          $sql = "select distinct ordhead.order_no as order_number, sups.sup_name as supplier_name, ordhead.ORIG_APPROVAL_DATE as approved_date,cgl_tickets_printed.reprint_required, ordhead.status
              from ordhead
                  left join cgl_tickets_printed on ordhead.order_no = cgl_tickets_printed.order_no
                  inner join ordloc on ordhead.order_no = ordloc.order_no and ordhead.status = 'A'
                  inner join ordsku on ordloc.order_no = ordsku.order_no and ordloc.item = ordsku.item
                  inner join item_master on item_master.item = ordloc.item
                  inner join deps on item_master.dept = deps.dept
                  inner join groups on deps.group_no = groups.group_no
                  inner join cgl_tickets_leadtime on Groups.Division = cgl_tickets_leadtime.Division
                  inner join sup_traits_matrix on ordhead.supplier = sup_traits_matrix.supplier and sup_traits_matrix.sup_trait = ".Config::get('ticket.supplier_trait')."
                  inner join sups on sups.supplier = ordhead.supplier";

          if ((!$this->user->isAdmin()) && (!$this->user->isWarehouse())) {
              $sql .= " and ordhead.supplier = :supplier";
          }

          $sql .= " where ordloc.QTY_Ordered > 0 and (cgl_tickets_printed.reprint_required = 'Y'";

          if (!$status) {
              $sql .= " or cgl_tickets_printed.reprint_required = 'N')";
          } else {
              $sql .= ")";
          }

          $sql .= " or (cgl_tickets_printed.order_no is null and ordhead.app_datetime is null )
              AND (ordhead.otb_eow_date between sysdate AND sysdate + cgl_tickets_leadtime.leaddays ) 
              order by ordhead.order_no";


          break;

        case 'SearchOrders':
          $sql = "select distinct ordhead.order_no as order_number, sups.sup_name as supplier_name, ordhead.ORIG_APPROVAL_DATE as approved_date, ordhead.status
              from ordhead
                  inner join ordloc on ordhead.order_no = ordloc.order_no 
                  inner join sups on sups.supplier = ordhead.supplier ";

          //if admin or warehouse no restriction on supplier else apply user supplier number to restrict what they can see
          if ((!$this->user->isAdmin()) && (!$this->user->isWarehouse())) {
              $sql .= "inner join sup_traits_matrix on ordhead.supplier = sup_traits_matrix.supplier and sup_traits_matrix.sup_trait = ".Config::get('ticket.supplier_trait')." and ordhead.supplier = :supplier";
          }

          $sql .= " where ordloc.QTY_Ordered > 0 ";

          if (($this->user->isAdmin()) || ($this->user->isWarehouse())) {
              $sql .= " and (ordhead.status = 'A' or ordhead.status = 'C')";
          } else {
              $sql .= " and (ordhead.status = 'A')";
          }

          $sql .= " and ordhead.order_no = :order_no";
             
          break;

        //External Ticket - Supplier Details Sql
        case 'Supplier':
        $sql = "SELECT sups.supplier as id, sups.sup_name as name, addr_type, addr.contact_name, addr.contact_phone, addr.contact_fax, addr.contact_email ,addr.add_1 as address_1, addr.add_2 as address_2, addr.add_3 as address_3, 
            addr.post, addr.city, addr.state, Country.Country_Desc as country_code 
          from sups 
              inner join addr on addr.module = 'SUPP' and addr.key_value_1 = sups.supplier
              inner join add_type on add_type.address_type = addr.addr_type and add_type.type_desc = '".ucfirst($type).
              "' inner join country on addr.country_id = country.country_id";
        
        //If not admin restrict by supplier orders
        if (!$this->user->isAdmin()) {
            $sql .= " where sups.supplier = :supplier";
        }

        break;

        //External Ticket - CartonPack Sql
        case 'CartonPack':
          $sql = "SELECT ordloc.order_no as order_number, PackStyle.Style, ordsku.pickup_no as pack_type, ordloc.item,  ordloc.QTY_Ordered as quantity, ordsku.PickUP_LOC as pick_location,
          item_master.item_desc as description, Groups.Group_Name, Deps.Dept_Name as department_name, Class.Class_Name, SubClass.Sub_Name as sub_class_name, ordhead.EDI_PO_IND as edi_po_index, 'CartonPack' as carton_type
          from ordhead
          inner join ordloc on ordhead.order_no = ordloc.order_no AND ordhead.status = 'A'
          inner join ordsku on ordloc.order_no = ordsku.order_no and ordloc.item = ordsku.item
          inner join item_master on item_master.item = ordloc.item and item_master.pack_ind = 'Y' and item_master.simple_pack_ind <> 'Y'
          inner join subclass on item_master.subclass = subclass.subclass and item_master.class = subclass.class and item_master.dept = subclass.dept
          inner join class on item_master.class = class.class and item_master.dept = class.dept
          inner join deps on item_master.dept = deps.dept
          inner join groups on deps.group_no = groups.group_no
          inner join ( select pack_no, max(item_parent) as Style from packitem group by pack_no ) PackStyle on ordloc.item = PackStyle.pack_no
          where ordloc.order_no = :order_no";

          if ($type) {
              $sql .= " AND ordloc.item = :item_number";
          }

          $sql .= " Order by  ordloc.Order_No , ordsku.pickup_no, ordloc.item";
        break;
        
        //External Ticket - Carton Simple Loose Pack
        case 'CartonLoose':
          $sql = "SELECT  ordhead.order_no as order_number, ordsku.item, item_master.item_parent as style, sizeDiff.Diff_Desc as item_size, 
          colour.diff_desc  as Colour, ordloc.QTY_Ordered as quantity, ordsku.PickUP_LOC as pick_location, 
          item_master.item_desc as description, ordhead.pickup_date, ordhead.Supplier, 'CartonLoose' as carton_type, 
          ordhead.EDI_PO_IND as edi_po_index 
          from ordhead 
          inner join ordloc on ordhead.order_no = ordloc.order_no AND ordhead.status = 'A'
          inner join ordsku on ordloc.order_no = ordsku.order_no and ordloc.item = ordsku.item 
          inner join item_master on item_master.item = ordloc.item and  item_master.pack_ind = 'N' 
          left join diff_ids colour on item_master.diff_1 = colour.diff_id and colour.diff_type = 'C' 
          left join diff_ids sizeDiff on item_master.diff_2 = sizeDiff.diff_id and sizeDiff.diff_type = 'S' 
          where ordhead.order_no = :order_no  and ordloc.QTY_Ordered > 0";

          if ($type) {
              $sql .= " AND ordloc.item = :item_number";
          }
          
          $sql .= " UNION 
          select ordhead.Order_No , ordsku.item, item.item_parent as style, sizeDiff.Diff_Desc, 
          colour.diff_desc  as Colour, ordloc.QTY_Ordered, ordsku.PickUP_LOC , 
          pack.item_desc , ordhead.pickup_date, ordhead.Supplier , 'SimplePack' as CartonType, ordhead.EDI_PO_IND
          from ordhead 
          inner join ordloc on ordhead.order_no = ordloc.order_no 
          inner join ordsku on ordloc.order_no = ordsku.order_no and ordloc.item = ordsku.item 
          inner join item_master pack on pack.item = ordloc.item and  pack.pack_ind = 'Y' and pack.simple_pack_ind = 'Y' 
          inner join packitem on ordloc.item = packitem.pack_no 
          inner join item_master item on packitem.item = item.item 
          left join diff_ids colour on item.diff_1 = colour.diff_id and colour.diff_type = 'C' 
          left join diff_ids sizeDiff on item.diff_2 = sizeDiff.diff_id and sizeDiff.diff_type = 'S'";
          
          $sql .= " where ordhead.order_no  = :order_no  and ordloc.QTY_Ordered > 0";
          
          if ($type) {
              $sql .= " AND ordloc.item = :item_number";
          }
        break;

        //External Ticket - Ratio Packs
        case 'RatioPack':
        $sql = "SELECT item_master.item_parent AS Style, ColourSEQ.Display_Seq AS Colour_SEQ, SizeSEQ.Display_Seq AS Size_SEQ, 'RatioPack' AS TYPE, ordhead.order_no as order_number
              ,ordloc.item AS Order_Item,item_master.item AS Item,Pack.item_desc AS Pack_Description,item_master.short_desc AS description,Barcodes.Item AS barcode
              ,Barcodes.Item_Number_Type AS barcode_type,ColourDiff.Diff_Desc AS colour,SizeDiff.DIFF_DESC AS item_size,coalesce(stockroomlocator.UDA_TEXT, '0000') AS
              stockroomlocator, division.div_name as division_name, coalesce(AUD.UNIT_RETAIL, -1) AS AUD, coalesce(NZD.Unit_Retail, -1) AS NZD, ordloc.qty_ordered as quantity, Packitem.PACK_QTY as Pack_Quantity
              , ordsku.earliest_ship_date, coalesce(sup_attributes.Pre_Ticket_Ind, 'N') AS Pre_Ticket_Ind,0 AS SimplePackItemTicketsReq,AUStore.Channel_Id
            FROM ordhead
            LEFT JOIN sup_attributes ON ordhead.supplier = sup_attributes.supplier
          INNER JOIN ordloc ON ordloc.order_no = ordhead.order_no
          INNER JOIN ordsku ON ordloc.order_no = ordsku.order_no AND OrdLoc.Item = ordsku.item
          INNER JOIN item_master Pack ON Pack.Item = ordsku.item AND Pack.pack_ind = 'Y' AND Pack.Simple_Pack_Ind = 'N'
          INNER JOIN packitem ON ordloc.item = packitem.pack_no
          INNER JOIN item_master ON item_master.item = packitem.item
          INNER JOIN Item_Master Barcodes ON Item_Master.Item = Barcodes.Item_Parent AND Barcodes.Primary_Ref_Item_Ind = 'Y'
          INNER JOIN diff_ids SizeDiff ON item_master.diff_2 = SizeDiff.diff_id AND SizeDiff.diff_type = 'S'
          INNER JOIN diff_group_detail SizeSEQ ON SizeSEQ.diff_id = SizeDiff.diff_id AND SizeSEQ.diff_group_id = 'SIZ1'
          INNER JOIN diff_ids ColourDiff ON item_master.diff_1 = ColourDiff.diff_id AND ColourDiff.diff_type = 'C'
          INNER JOIN diff_group_detail ColourSEQ ON ColourSEQ.diff_id = ColourDiff.diff_id AND ColourSEQ.diff_group_id = 'CLR1'
            LEFT JOIN uda_item_ff stockroomlocator ON stockroomlocator.item = item_master.item AND stockroomlocator.uda_id = 900
          INNER JOIN deps ON item_master.dept = deps.dept
          INNER JOIN groups ON deps.group_no = groups.group_no
          INNER JOIN division ON Groups.Division = Division.Division
          INNER JOIN store AUStore ON ((AUStore.default_wh = ordloc.location AND ordloc.loc_type = 'W') OR (AUStore.store = ordloc.location AND
                    ordloc.loc_type = 'S'))
          INNER JOIN CGL_RPT_CHANNEL_ZONE_MAP AUStoreZone ON AUStore.country_id = 'AU' AND (AUStore.store = AUStoreZone.Primary_Store OR (AUStore.store 
          in(1800,2800,20800) and AUStoreZone.channel_id = AUStore.channel_id))
          INNER JOIN item_zone_price AUD ON AUD.zone_group_id = AUStoreZone.zone_group_id AND AUD.zone_id = AUStoreZone.zone_id
                    AND AUD.item = item_master.item
            LEFT JOIN item_zone_price NZD ON NZD.ZONE_GROUP_ID = 1 AND NZD.zone_id = 4 AND NZD.ITEM = item_master.item
          WHERE ordhead.order_no = :order_no AND ordloc.qty_ordered > 0
          ORDER BY Style, Colour_SEQ, Size_SEQ ";
            break;

        //External Ticket - Loose Items Sql
        case 'LooseItem':
        $sql = "SELECT item_master.item_parent AS Style, ColourSEQ.Display_Seq AS Colour_SEQ, SizeSEQ.Display_Seq AS Size_SEQ, 'LooseItem' AS TYPE
              ,ordhead.order_no as order_number, ordsku.item AS Order_Item, item_master.item, '' AS Pack_Description, item_master.short_desc AS description
              ,Barcodes.Item AS barcode, Barcodes.Item_Number_Type AS barcode_type, ColourDiff.Diff_Desc AS colour, SizeDiff.DIFF_DESC AS item_size
            ,coalesce(stockroomlocator.UDA_TEXT, '0000') AS stockroomlocator, division.div_name as division_name, coalesce(AUD.UNIT_RETAIL, -1) AS AUD,
            coalesce(NZD.Unit_Retail, -1) AS NZD, ordloc.qty_ordered as quantity, 1 AS Pack_Quantity, ordsku.earliest_ship_date,
            coalesce(sup_attributes.Pre_Ticket_Ind, 'N') AS Pre_Ticket_Ind, 0 AS SimplePackItemTicketsReq, AUStore.Channel_Id
            FROM ordhead
            LEFT JOIN sup_attributes ON ordhead.supplier = sup_attributes.supplier
          INNER JOIN ordloc ON ordloc.order_no = ordhead.order_no
          INNER JOIN ordsku ON ordloc.order_no = ordsku.order_no AND OrdLoc.Item = ordsku.item
          INNER JOIN item_master ON item_master.item = ordsku.item AND item_master.pack_ind = 'N'
          INNER JOIN Item_Master Barcodes ON Item_Master.Item = Barcodes.Item_Parent AND Barcodes.Primary_Ref_Item_Ind = 'Y'
          INNER JOIN diff_ids SizeDiff ON item_master.diff_2 = SizeDiff.diff_id AND SizeDiff.diff_type = 'S'
          INNER JOIN diff_group_detail SizeSEQ ON SizeSEQ.diff_id = SizeDiff.diff_id AND SizeSEQ.diff_group_id = 'SIZ1'
          INNER JOIN diff_ids ColourDiff ON item_master.diff_1 = ColourDiff.diff_id AND ColourDiff.diff_type = 'C'
          INNER JOIN diff_group_detail ColourSEQ ON ColourSEQ.diff_id = ColourDiff.diff_id AND ColourSEQ.diff_group_id = 'CLR1'
          LEFT JOIN uda_item_ff stockroomlocator ON stockroomlocator.item = item_master.item AND stockroomlocator.uda_id = 900
          INNER JOIN deps ON item_master.dept = deps.dept
          INNER JOIN groups ON deps.group_no = groups.group_no
          INNER JOIN division ON Groups.Division = Division.Division
          INNER JOIN store AUStore ON ((AUStore.default_wh = ordloc.location AND ordloc.loc_type = 'W') OR
                  (AUStore.store = ordloc.location AND ordloc.loc_type = 'S'))
          INNER JOIN CGL_RPT_CHANNEL_ZONE_MAP AUStoreZone ON AUStore.country_id = 'AU'
                    AND (AUStore.store = AUStoreZone.Primary_Store OR (AUStore.store in(1800,2800,20800) and AUStoreZone.channel_id = AUStore.channel_id))
          INNER JOIN item_zone_price AUD ON AUD.zone_group_id = AUStoreZone.zone_group_id AND AUD.zone_id = AUStoreZone.zone_id
                    AND AUD.item = item_master.item
            LEFT JOIN item_zone_price NZD ON NZD.ZONE_GROUP_ID = 1 AND NZD.zone_id = 4 AND NZD.ITEM = item_master.item
          WHERE ordhead.order_no = :order_no AND ordloc.qty_ordered > 0
          ORDER BY Style, Colour_SEQ, Size_SEQ";
        break;

        //External Ticket - Simple Packs
        case 'SimplePack':
        $sql = "SELECT item_master.item_parent AS Style, ColourSEQ.Display_Seq AS Colour_SEQ, SizeSEQ.Display_Seq AS Size_SEQ, 'SimplePack' AS TYPE
              ,ordhead.order_no as order_number, ordsku.item AS OrderItem, item_master.item AS Item, Pack.item_desc AS Pack_Description, item_master.short_desc AS description
              ,PackBarcodes.Item AS Pack_Barcode, PackBarcodes.Item_Number_Type AS Pack_barcode_type, Barcodes.Item AS barcode
              ,Barcodes.Item_Number_Type AS barcode_type, ColourDiff.Diff_Desc AS colour, SizeDiff.DIFF_DESC AS item_size
              ,coalesce(stockroomlocator.UDA_TEXT, 'PACK') AS stockroomlocator, division.div_name as division_name, coalesce(AUD.UNIT_RETAIL, -1) AS AUD
            ,coalesce(NZD.Unit_Retail, -1) AS NZD, ordloc.qty_ordered as quantity, Packitem.Pack_Qty as pack_quantity, ordsku.earliest_ship_date
            ,coalesce(sup_attributes.Pre_Ticket_Ind, 'N') AS Pre_Ticket_Ind
            ,coalesce(decode(sup_traits_matrix.sup_trait, 9008, 1, 0), 0) AS SimplePackItemTicketsReq, AUStore.Channel_Id
            FROM ordhead
          LEFT JOIN sup_attributes ON ordhead.supplier = sup_attributes.supplier
          LEFT JOIN sup_traits_matrix ON ordhead.supplier = sup_traits_matrix.supplier AND sup_traits_matrix.sup_trait = 9008
          INNER JOIN ordloc ON OrdHead.Order_No = ordloc.order_no
          INNER JOIN ordsku ON ordhead.order_no = ordsku.order_no AND ordloc.item = ordsku.item AND ordloc.qty_ordered > 0
          INNER JOIN item_master Pack ON Pack.Item = ordloc.item AND Pack.pack_ind = 'Y' AND Pack.Simple_Pack_Ind = 'Y'
          INNER JOIN packitem ON ordloc.item = packitem.pack_no
          INNER JOIN item_master ON item_master.item = packitem.item
          INNER JOIN Item_Master PackBarcodes ON PackBarcodes.item_parent = ordsku.item AND PackBarcodes.Primary_Ref_Item_Ind = 'Y'
          INNER JOIN Item_Master Barcodes ON Barcodes.item_parent = packitem.item AND Barcodes.Primary_Ref_Item_Ind = 'Y'
          INNER JOIN diff_ids SizeDiff ON item_master.diff_2 = SizeDiff.diff_id AND SizeDiff.diff_type = 'S'
          INNER JOIN diff_group_detail SizeSEQ ON SizeSEQ.diff_id = SizeDiff.diff_id AND SizeSEQ.diff_group_id = 'SIZ1'
          INNER JOIN diff_ids ColourDiff ON item_master.diff_1 = ColourDiff.diff_id AND ColourDiff.diff_type = 'C'
          INNER JOIN diff_group_detail ColourSEQ ON ColourSEQ.diff_id = ColourDiff.diff_id AND ColourSEQ.diff_group_id = 'CLR1'
          LEFT JOIN uda_item_ff stockroomlocator ON stockroomlocator.item = item_master.item AND stockroomlocator.uda_id = 900
          INNER JOIN deps ON item_master.dept = deps.dept
          INNER JOIN groups ON deps.group_no = groups.group_no
          INNER JOIN division ON Groups.Division = Division.Division
          INNER JOIN store AUStore ON ((AUStore.default_wh = ordloc.location AND ordloc.loc_type = 'W') OR
          (AUStore.store = ordloc.location AND ordloc.loc_type = 'S'))
          INNER JOIN CGL_RPT_CHANNEL_ZONE_MAP AUStoreZone ON AUStore.country_id = 'AU'
                    AND (AUStore.store = AUStoreZone.Primary_Store OR (AUStore.store in(1800,2800,20800) and AUStoreZone.channel_id = AUStore.channel_id))
          INNER JOIN item_zone_price AUD ON AUD.zone_group_id = AUStoreZone.zone_group_id AND AUD.zone_id = AUStoreZone.zone_id
                    AND AUD.item = item_master.item
            LEFT JOIN item_zone_price NZD ON NZD.ZONE_GROUP_ID = 1 AND NZD.zone_id = 4 AND NZD.ITEM = item_master.item
          WHERE ordhead.order_no = :order_no
          ORDER BY Style, Colour_SEQ, Size_SEQ ";
        break;
        
        //Warehouse Ticket - Orderdetails
        case 'orderdetails':
          $sql = "SELECT  ordhead.order_No , item_master.item_parent, ordsku.item, ordloc.location, ordloc.loc_type, ordloc.qty_Ordered, ordsku.origin_country_id,
                  ordloc.unit_retail, item_master.pack_ind, item_master.simple_pack_ind
          from ordhead
          inner join ordloc on ordhead.order_no = ordloc.order_no AND ordhead.status = 'A'
          inner join ordsku on ordloc.order_no = ordsku.order_no and ordloc.item = ordsku.item
          inner join item_master on item_master.item = ordloc.item
          where ordhead.order_no = :order_no ";
          break;
        
        case 'ticketrequests':
          $sql = "SELECT  tr.Order_No, tr.Ticket_Type_ID, tr.Sort_Order_Type, tr.Printer_Type, tr.Qty As Quantity, 
                  tr.Location, im.item as itemnumber, im.pack_ind, im.simple_pack_ind, diff_1, diff_2, pack_type as packtype
              FROM ticket_request tr
              INNER JOIN item_master im ON tr.item = im.item
              WHERE tr.item = :item_number AND tr.order_no = :order_no
              ORDER BY tr.Order_No, tr.Ticket_Type_ID, tr.Sort_Order_Type, tr.Printer_Type";
        break;
        
        case 'ticketitem':
        $sql = "SELECT  im.item_parent AS productnumber, im.item as itemnumber, im.short_desc, sz.diff_desc AS item_size, cl.diff_desc AS colour, 
                1 AS quantity, srl.uda_text as stockroom, v.uda_value_desc  AS brand, os.earliest_ship_date, br.item AS barcode, 99999 AS sortid, 
                99999 AS sortnumid, NVL(os.pickup_no, 'ZZZ') AS packtype, 
                CASE WHEN EXISTS (SELECT wh FROM wh WHERE wh = :location1) 
                THEN (SELECT NVL(i.unit_retail, 0) AS unit_retail 
                FROM   item_zone_price i, price_zone p 
                WHERE  p.zone_id = i.zone_id 
                AND    im.item = i.item 
                AND    p.zone_group_id = im.retail_zone_group_id 
                AND    i.zone_id = (SELECT z.zone_id 
                FROM store s 
                INNER JOIN price_zone_group_store z ON s.store = z.store AND s.country_id  = 'AU'
                WHERE  s.default_wh = :location2 
                AND rownum = 1)) 
                ELSE (SELECT NVL(i.unit_retail, 0) AS unit_retail 
                FROM   item_zone_price i
                INNER JOIN price_zone p ON p.zone_id = i.zone_id 
                INNER JOIN price_zone_group_store z ON i.zone_id = z.zone_id
                WHERE  im.item = i.item 
                AND    p.zone_group_id = im.retail_zone_group_id
                AND    z.store = :location3)
                END AS auprice,
                NVL(nz.unit_retail, 0) AS nzprice 
                FROM  item_master im 
                INNER JOIN ordsku os ON os.item = im.item 
                LEFT JOIN diff_ids sz ON im.diff_2 = sz.diff_id and sz.diff_type = 'S'
                LEFT JOIN diff_ids cl ON im.diff_1 = cl.diff_id and cl.diff_type = 'C'
                LEFT JOIN uda_item_ff srl ON srl.item = im.item and srl.uda_id = 900
                LEFT JOIN uda_item_lov uil ON uil.item = im.item
                INNER JOIN uda_values v ON uil.uda_id = v.uda_id AND uil.uda_value = v.uda_value AND v.uda_id = 8
                LEFT JOIN item_master br ON br.item_parent = im.item AND br.primary_ref_item_ind = 'Y'
                LEFT JOIN item_zone_price nz ON nz.item = im.item and nz.zone_id = 4
                WHERE os.order_no = :ordernumber AND im.item = :item_number";
        break;

        //Warehouse Ticket - simple pack details
        case 'ticketsimplepack':
        $sql = "SELECT pi.pack_no AS productnumber, im.item as itemnumber, im.short_desc, sz.diff_desc AS item_size, cl.diff_desc AS colour, 
                1 AS quantity, srl.uda_text as stockroom, v.uda_value_desc  AS brand, os.earliest_ship_date, br.item AS barcode, 
                99999 AS sortid, 99999 AS sortnumid, NVL(os.pickup_no, 'ZZZ') AS packtype, 0 as auprice, 0 as nzprice
                FROM item_master im
                INNER JOIN packitem pi ON im.item = pi.item
                INNER JOIN ordsku os ON os.item = pi.pack_no 
                LEFT JOIN diff_ids sz ON im.diff_2 = sz.diff_id and sz.diff_type = 'S'
                LEFT JOIN diff_ids cl ON im.diff_1 = cl.diff_id and cl.diff_type = 'C'
                LEFT JOIN uda_item_ff srl ON srl.item = im.item and srl.uda_id = 900
                LEFT JOIN uda_item_lov uil ON uil.item = im.item
                INNER JOIN uda_values v ON uil.uda_id = v.uda_id AND uil.uda_value = v.uda_value AND v.uda_id = 8
                INNER JOIN item_master br ON br.item_parent = pi.pack_no AND br.primary_ref_item_ind = 'Y'
                WHERE os.order_no = :order_no AND pi.pack_no = :packnumber";
        break;

        case 'ticketpack':
        $sql = "SELECT im.item as itemnumber, pk.item_parent AS productnumber, pk.pack_no, im.short_desc, sz.diff_desc AS item_size, 
              cl.diff_desc AS colour, pk.pack_qty AS quantity, srl.uda_text AS stockroom, v.uda_value_desc  AS brand, os.earliest_ship_date, 
              br.item AS barcode, 99999 AS sortid, 99999 AS sortnumid, NVL(os.pickup_no, 'ZZZ') AS packtype,
             CASE WHEN EXISTS (SELECT wh FROM wh WHERE wh = :location1) 
              THEN (SELECT NVL(i.unit_retail, 0) AS unit_retail 
                    FROM   item_zone_price i, price_zone p 
                    WHERE  p.zone_id = i.zone_id 
                    AND    pk.item = i.item 
                    AND    p.zone_group_id = im.retail_zone_group_id 
                    AND    i.zone_id = (SELECT z.zone_id 
                                        FROM store s 
                                        INNER JOIN price_zone_group_store z ON s.store = z.store AND s.country_id  = 'AU'
                                        WHERE  s.default_wh = :location2 
                                        AND rownum = 1)) 
             ELSE (SELECT NVL(i.unit_retail, 0) AS unit_retail 
                   FROM    item_zone_price i
                   INNER JOIN price_zone p ON p.zone_id = i.zone_id 
                   INNER JOIN price_zone_group_store z ON i.zone_id = z.zone_id
                   WHERE  pk.item = i.item 
                   AND   p.zone_group_id = im.retail_zone_group_id
                   AND   z.store = :location3)
             END AS auprice,
            NVL(nz.unit_retail, 0) AS nzprice 
            FROM  packitem pk
            INNER JOIN item_master im ON pk.item = im.item 
            INNER JOIN ordsku os ON pk.pack_no = os.item
            LEFT JOIN diff_ids sz ON im.diff_2 = sz.diff_id and sz.diff_type = 'S'
            LEFT JOIN diff_ids cl ON im.diff_1 = cl.diff_id and cl.diff_type = 'C'
            LEFT JOIN uda_item_ff srl ON srl.item = im.item and srl.uda_id = 900
            LEFT JOIN uda_item_lov uil ON uil.item = im.item
            INNER JOIN uda_values v ON uil.uda_id = v.uda_id AND uil.uda_value = v.uda_value AND v.uda_id = 8
            LEFT JOIN item_master br ON br.item_parent = im.item AND br.primary_ref_item_ind = 'Y'
            LEFT JOIN item_zone_price nz ON nz.item = im.item and nz.zone_id = 4
            WHERE os.order_no = :order_no and pk.pack_no = :packnumber";
        break;

        case 'deleteloosecartons':
          $sql = "delete  ticket_request 
                  where ticket_request.ticket_type_id = 'CTRN' and ticket_request.order_no in 
              ( select  distinct ordhead.Order_No 
                from ticket_request 
                     inner join ordloc on  ordloc.item = ticket_request.item and ordloc.location = ticket_request.location and ticket_request.ticket_type_id = 'CTRN' 
                     inner  join ordhead on Ordloc.Order_No = ordhead.order_no 
                     inner join ordsku on ordloc.order_no = ordsku.order_no and ordloc.item = ordsku.item 
                     inner join item_master on item_master.item = ordloc.item and ( item_master.pack_ind = 'N' or item_master.simple_pack_ind = 'Y' ) 
                where ordsku.PickUP_LOC is null and ordloc.QTY_Ordered > 0 and ticket_request.order_no = :order_no)";
        break;
        
        case 'ticketcartonpack':
          $sql = "select ticket_request.Create_DateTime, ticket_request.Ticket_Type_ID, ticket_request.Order_No as order_number, ticket_request.Printer_Type, 
                  PackStyle.Style, ordsku.pickup_no as pack_type, ticket_request.item, ticket_request.QTY as overprint_quantity, ordloc.QTY_Ordered as quantity, ordsku.PickUP_LOC as pick_location, 
                  item_master.item_desc as description, Groups.Group_Name, Deps.Dept_Name as department_name, Class.Class_Name, SubClass.Sub_Name as sub_class_name, ordloc.loc_type as location_type, ordloc.location 
                  from ticket_request 
                  inner join ordloc on ticket_request.order_no = ordloc.order_no and ordloc.item = ticket_request.item and ordloc.location = ticket_request.location
                  inner join ordsku on ordloc.order_no = ordsku.order_no and ordloc.item = ordsku.item 
                  inner join item_master on item_master.item = ordloc.item and item_master.pack_ind = 'Y' and item_master.simple_pack_ind <> 'Y'
                  inner join subclass on item_master.subclass = subclass.subclass and item_master.class = subclass.class and item_master.dept = subclass.dept 
                 inner join class on item_master.class = class.class and item_master.dept = class.dept 
                 inner join deps on item_master.dept = deps.dept 
                 inner join groups on deps.group_no = groups.group_no 
                 inner join ( select pack_no, max(item_parent) as Style from packitem group by pack_no ) PackStyle on ticket_request.item = PackStyle.pack_no 
                  where ticket_request.ticket_type_ID = 'CTRN' AND ticket_request.order_no = :order_no
                  Order by  ticket_request.Order_No , item_master.Pack_Type, ticket_request.item";
        break;

        case 'ticketcartonloose':
          $sql = " select ticket_request.Create_DateTime, ticket_request.Order_No as order_number, ticket_request.Ticket_Type_ID,   ticket_request.Printer_Type, 
                  ticket_request.item, item_master.item_parent as style, sizeDiff.Diff_Desc as item_size, 
                  colour.diff_desc  as Colour, ticket_request.QTY as overprint_quantity, ordloc.QTY_Ordered as quantity, ordsku.PickUP_LOC as pick_location, 
                  item_master.item_desc as description, ordhead.pickup_date, ordhead.Supplier 
                  from ticket_request 
                  inner join ordhead on ticket_request.order_no = ordhead.order_no 
                  inner join ordloc on ticket_request.order_no = ordloc.order_no and ordloc.item = ticket_request.item and ordloc.location = ticket_request.location 
                  inner join ordsku on ordloc.order_no = ordsku.order_no and ordloc.item = ordsku.item 
                  inner join item_master on item_master.item = ordloc.item and  item_master.pack_ind = 'N' 
                  left join diff_ids colour on item_master.diff_1 = colour.diff_id and colour.diff_type = 'C' 
                  left join diff_ids sizeDiff on item_master.diff_2 = sizeDiff.diff_id and sizeDiff.diff_type = 'S' 
                  where ticket_request.ticket_type_ID = 'CTRN' 
                  AND ( ticket_request.Order_No = :order_no OR :order_no is null ) 
                UNION 
                  select ticket_request.Create_DateTime, ticket_request.Order_No , ticket_request.Ticket_Type_ID,   ticket_request.Printer_Type, 
                  ticket_request.item, item.item_parent as style, sizeDiff.Diff_Desc as item_size, 
                  colour.diff_desc  as Colour, ticket_request.QTY as PrintQTY, ordloc.QTY_Ordered, ordsku.PickUP_LOC , 
                  pack.item_desc , ordhead.pickup_date, ordhead.Supplier 
                  from ticket_request 
                  inner join ordhead on ticket_request.order_no = ordhead.order_no 
                  inner join ordloc on ticket_request.order_no = ordloc.order_no and ordloc.item = ticket_request.item and ordloc.location = ticket_request.location 
                  inner join ordsku on ordloc.order_no = ordsku.order_no and ordloc.item = ordsku.item 
                  inner join item_master pack on pack.item = ordloc.item and  pack.pack_ind = 'Y' and pack.simple_pack_ind = 'Y' 
                  inner join packitem on ordloc.item = packitem.pack_no 
                  inner join item_master item on packitem.item = item.item 
                  left join diff_ids colour on item.diff_1 = colour.diff_id and colour.diff_type = 'C' 
                  left join diff_ids sizeDiff on item.diff_2 = sizeDiff.diff_id and sizeDiff.diff_type = 'S' 
                  where ticket_request.ticket_type_ID = 'CTRN' 
                  AND ( ticket_request.Order_No = :order_no OR :order_no is null )";
        break;
        default:
          $sql = "SELECT * FROM DUAL";
          break;
      }

        return $sql;
    }
}
