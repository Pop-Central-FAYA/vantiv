<?php

namespace Vanguard\Http\Controllers\Api\Playout;

use Illuminate\Http\Request;
use Vanguard\Services\BroadcasterPlayout\DailyPlayoutProvider;
use Vanguard\Libraries\Enum\BroadcasterPlayoutStatus as PlayoutStatus;
use Vanguard\Models\BroadcasterPlayout as Playout;

class PlayoutController extends BaseController {


    /**
     * This will get the daily playouts, grouped by the relevant adblocks
     * This will only return the daily playouts that have files which have been downloaded
     * For now
     * Since there is a weeks delay or so before ads play
     * @return [type]
     */
    public function getPlayouts(Request $request) {
        if ($this->verifyThatHeaderIsCorrect($request)) {
            $air_date = $request->input('air_date');
            $broadcaster_id = 'random';

            $provider = new DailyPlayoutProvider($broadcaster_id, $air_date);
            $playout_list = $provider->getAll();
            return response()->json(['data' => $playout_list], 200);

        }
        return $this->invalidAuthentication();
    }

    /**
     * Update that the playout has been placed
     * @return [type]
     */
    public function updateAdPlaced(Request $request, $playout_id) {
        if ($this->verifyThatHeaderIsCorrect($request)) {
            $playout = Playout::find($playout_id);
            if (!$playout) {
                return $this->resourceNonExistent('playout');
            }

            $playout->status = PlayoutStatus::PLACED;
            $playout->placed_at = $request->input('placed_at');
            $playout->placed_in = $request->input('placed_in');
            $playout->save();
            return response()->json(['data' => []], 200);
        }
        return $this->invalidAuthentication();
    }

    /**
     * Update that the playout has been placed
     * @return [type]
     */
    public function updateAdPlayed(Request $request, $playout_id) {
        if ($this->verifyThatHeaderIsCorrect($request)) {
            $playout = Playout::find($playout_id);
            if (!$playout) {
                return $this->resourceNonExistent('playout');
            }

            $playout->status = PlayoutStatus::PLAYED;
            $playout->played_at = $request->input('played_at');
            $playout->save();
            return response()->json(['data' => []], 200);
        }
        return $this->invalidAuthentication();
    }

}
