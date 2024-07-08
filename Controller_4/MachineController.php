<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Machines;
use App\Models\MachineVoltages;
use App\Exports\MachineExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class MachineController extends Controller
{

    // register new  machine
    public function register(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                //   'unique_id' => 'required',
                'machines' => 'required',


            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'code' => '302', 'data' => $validator->errors()]);
            }


            $machine = new Machines();
            $machine->unique_id = $request->unique_id;
            $machine->machines = $request->machines;
            $machine->machine_id = $request->machine_id;
            $machine->refrence_id = $request->refrence_id;
            $machine->save();

            // $MachineVoltages = $request->input('MachineVoltages');

            // foreach ($MachineVoltages as $key => $value) {

            //     $MachineVoltages = new MachineVoltages;
            //     $MachineVoltages->machine_id = $request->machine_id;
            //     // $MachineVoltages->refrence_id = $request->reference_id;

            //     $MachineVoltages->date = $value['date'];
            //     $MachineVoltages->time = $value['time'];
            //     $MachineVoltages->sent_1_voltage = $value['sent_1_voltage'];
            //     $MachineVoltages->sent_2_c = $value['sent_2_c'];

            //     $MachineVoltages->save();
            // }

            return response()->json(['status' => 'success', 'code' => '200', 'message' => 'Machine Add Successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => '302', 'data' => $e->getMessage()]);
        }
    }


    public function Machines()
    {
        try {

            $machine = Machines::select('id', 'unique_id', 'machine_id', 'refrence_id', 'created_at')->orderBy('id', 'desc')->get();
            return response()->json(['status' => 'success', 'code' => '200', 'data' => $machine]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => '302', 'data' => $e->getMessage()]);
        }
    }

    public function MachinesDetails(Request $request, $id)
    {
        try {

            $machine = Machines::where('id', $id)->pluck('machines')->first();

            return response()->json(['status' => 'success', 'code' => '200', 'data' => $machine]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => '302', 'data' => $e->getMessage()]);
        }
    }


    public function delete(Request $request, $id)
    {
        try {

            $machine = Machines::where('id', $request->id)->delete();

            return response()->json(['status' => 'success', 'code' => '200', 'data' => 'Machine deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => '302', 'data' => $e->getMessage()]);
        }
    }
    // export excel sheet
    public function export(Request $request, $id)
    {
        try {

            $id = $request->id;

            $data = Machines::where('id', $id)->get(['id', 'unique_id', 'machines', 'created_at', 'updated_at']);

            if (!empty($data)) {
                $dataArray = [];
                $result = [];
                foreach ($data as $datas) {
                    $name = $datas->id;
                    $data = json_decode($datas->machines, true);
                    $i = 0;
                    $currentMap = [];
                    foreach ($data['current'] as $current) {
                        $currentMap[] = $current['current'];
                    }
                    foreach ($data['voltage'] as $voltage) {
                        $timeStamp = $voltage['timeStamp'];

                        $timeStampSeconds = intval($timeStamp) / 1000;
                        $Date = date("Y-m-d ", $timeStampSeconds);
                        $Time = date("H:i:s", $timeStampSeconds); // 
                        $dataArray[] = [
                            "Date" => $Date,
                            "Time" => $Time,
                            "Current" => $currentMap[$i]     . ' Amp',
                            "voltage" => $voltage['voltage'] . ' Volt'
                        ];
                        $i++;
                    }
                }
            }
            // return $dataArray;
            $data = Machines::where('id', $id)->get(['id', 'unique_id', 'machines', 'created_at', 'updated_at']);

            $machin = [];
            foreach ($data as $datas) {
                $machines = json_decode($datas->machines, true);
                $machin['machinId']   = 'MachineId :          ' . $machines['machineId'];
                $machin['refrenceId'] = 'RefrenceId :         ' . $machines['referenceId'];
                $machin['maxCurrent'] = 'maxCurrent :         ' . $machines['range']['maxCurrent'];
                $machin['minCurrent'] = 'minCurrent :         ' . $machines['range']['minCurrent'];
                $machin['maxVoltage'] = 'maxVoltage :         ' . $machines['range']['maxVoltage'];
                $machin['minVoltage'] = 'minVoltage :         ' . $machines['range']['minVoltage'];
            }
            $uniqueValue = now()->format('YmdHisu');
            $excelFileName = 'machines_' . $uniqueValue . '.xlsx';

            Excel::store(new MachineExport($dataArray, $machin), $excelFileName, 'public');
            // $path = Storage::url($excelFileName);
            $path = asset('/storage/' . $excelFileName);


            return response()->json(['status' => 'success', 'code' => '200', 'data' => $path]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'code' => '302', 'data' => $e->getMessage()]);
        }
    }
}
