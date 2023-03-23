<?php

namespace App\Exports;

use Auth;
use App\Shipment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShipmentsExportExcel implements FromCollection,WithHeadings,WithStyles
{
    public function __construct(string $states)
    {
        $this->states = $states;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function headings(): array
    {
        if(Auth::user()->user_type == 'customer'){
            // return ["Code", "Type", "Status", "Branch", "Shipping Date", "Client Address", "Client Phone", "Reciver Name", "Reciver Phone", "Reciver Address" ,'From Country','To Country','From State','To State','From Area','To Area' , "Payment Type", "Payment Method", "Tax", "Insurance", "Shipping Cost", "Delivery Time", "Total Weight", "Amount To Be Collected", "Order Id" ,'Created At'];
            return ["Code", "Type", "Status", "Shipping Date", "Client Address", "Client Phone", "Reciver Name", "Reciver Phone", "Reciver Address" , 'From State','To State','From Area','To Area' , "Tax", "Insurance", "Shipping Cost", "Delivery Time",  "Amount To Be Collected", "Order Id" ,'Created At'];
        }elseif(Auth::user()->user_type == 'branch'){
            return ["Code", "Type", "Status", "Client", "Shipping Date", "Client Address", "Client Phone", "Reciver Name", "Reciver Phone", "Reciver Address" ,'From Country','To Country','From State','To State','From Area','To Area' , "Payment Type", "Payment Method", "Tax", "Insurance", "Shipping Cost", "Delivery Time", "Total Weight", "Amount To Be Collected", "Order Id" ,'Created At'];
        }else {
            // return ["Code", "Type", "Status", "Branch", "Client", "Shipping Date", "Client Address", "Client Phone", "Reciver Name", "Reciver Phone", "Reciver Address" ,'From Country','To Country','From State','To State','From Area','To Area' , "Payment Type", "Payment Method", "Tax", "Insurance", "Shipping Cost", "Delivery Time", "Total Weight", "Amount To Be Collected", "Order Id" ,'Created At'];
            return ["Code", "Type", "Status",  "Client", "Shipping Date", "Client Address", "Client Phone", "Reciver Name", "Reciver Phone", "Reciver Address" ,'From State','To State','From Area','To Area' ,"Tax", "Insurance", "Shipping Cost", "Delivery Time",  "Amount To Be Collected", "Order Id" ,'Created At'];
        }
    }

    public function collection()
    {
        if($this->states == 'all')
        {
            $shipments = Shipment::where('id','!=', null );
        }else {
            $shipments = Shipment::where('status_id', $this->states);
        }

        if(Auth::user()->user_type == 'customer'){
            // $shipments = $shipments->select('code','type','status_id','branch_id','shipping_date','client_address','client_phone','reciver_name','reciver_phone','reciver_address','from_country_id','to_country_id','from_state_id','to_state_id','from_area_id','to_area_id','payment_type','payment_method_id','tax','insurance','shipping_cost','delivery_time','total_weight','amount_to_be_collected','order_id','created_at');
            $shipments = $shipments->select('code','type','status_id','shipping_date','client_address','client_phone','reciver_name','reciver_phone','reciver_address','from_state_id','to_state_id','from_area_id','to_area_id','tax','insurance','shipping_cost','delivery_time','amount_to_be_collected','order_id','created_at');
            $shipments = $shipments->where('client_id', Auth::user()->userClient->client_id);
        }elseif(Auth::user()->user_type == 'branch'){
            $shipments = $shipments->select('code','type','status_id','client_id','shipping_date','client_address','client_phone','reciver_name','reciver_phone','reciver_address','from_country_id','to_country_id','from_state_id','to_state_id','from_area_id','to_area_id','payment_type','payment_method_id','tax','insurance','shipping_cost','delivery_time','total_weight','amount_to_be_collected','order_id','created_at');
            $shipments = $shipments->where('branch_id', Auth::user()->userBranch->branch_id);
        }else {
            // $shipments = $shipments->select('code','type','status_id','branch_id','client_id','shipping_date','client_address','client_phone','reciver_name','reciver_phone','reciver_address','from_country_id','to_country_id','from_state_id','to_state_id','from_area_id','to_area_id','payment_type','payment_method_id','tax','insurance','shipping_cost','delivery_time','total_weight','amount_to_be_collected','order_id','created_at');
            $shipments = $shipments->select('code','type','status_id','client_id','shipping_date','client_address','client_phone','reciver_name','reciver_phone','reciver_address','from_state_id','to_state_id','from_area_id','to_area_id','tax','insurance','shipping_cost','delivery_time','amount_to_be_collected','order_id','created_at');
        }
        $shipments = $shipments->with('pay')->orderBy('id','DESC')->get();

        foreach($shipments as $shipment)
        {
            $shipment->status_id = $shipment->getStatus();
            if(Auth::user()->user_type != 'customer')
            {
                $shipment->client_id = $shipment->client->name;
            }
            $shipment->client_address = $shipment->from_address->address;
            $shipment->created_at     = $shipment->created_at->format('Y-m-d');

            $shipment->from_state_id   = $shipment->from_state->name;
            $shipment->to_state_id     = $shipment->to_state->name;
            if($shipment->from_area_id != null)
            {
                $shipment->from_area_id    = $shipment->from_area->name;
            }
            if($shipment->to_area_id != null)
            {
                $shipment->to_area_id    = $shipment->to_area->name;
            }
            $shipment->payment_type      = $shipment->getPaymentType();
            $shipment->payment_method_id = $shipment->pay['name'];
        }

        return $shipments;
    }
}
