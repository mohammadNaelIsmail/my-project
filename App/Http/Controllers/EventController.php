<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Events",
 *     description="API Endpoints for Events"
 * )
 */
class EventController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/events",
     *     tags={"Events"},
     *     summary="Get list of events with pagination",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Event")
     *             ),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
{
    $search = $request->input('search');
    $perPage = $request->input('per_page', 10); 

    $query = Event::query();

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    $events = $query->paginate($perPage);

    return response()->json($events, 200);
}


    /**
     * @OA\Post(
     *     path="/api/events",
     *     tags={"Events"},
     *     summary="Create a new event",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Event created",
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string',
            'event_img' => 'nullable|string',
            'revenue' => 'nullable|numeric',
            'start_day' => 'required|date',
            'end_day' => 'required|date|after_or_equal:start_day',
            'start_hour' => 'required|string',
            'end_hour' => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $event = Event::create($request->all());
        return response()->json($event, 201);
    }
    /**
     * @OA\Get(
     *     path="/api/events/{id}",
     *     tags={"Events"},
     *     summary="Get an event by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Event ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event found",
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Response(response=404, description="Event not found")
     * )
     */
    public function show($id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['error' => 'Event not found'], 404);
        }
        return response()->json($event, 200);
    }
    /**
     * @OA\Put(
     *     path="/api/events/{id}",
     *     tags={"Events"},
     *     summary="Update an event",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Event ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event updated",
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Response(response=404, description="Event not found"),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'location' => 'sometimes|string|max:255',
            'title' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|max:50',
            'category' => 'sometimes|string|max:50',
            'description' => 'nullable|string',
            'event_img' => 'nullable|string',
            'revenue' => 'nullable|numeric',
            'start_day' => 'sometimes|date',
            'end_day' => 'sometimes|date|after_or_equal:start_day',
            'start_hour' => 'sometimes|string',
            'end_hour' => 'sometimes|string',
            'ticket_id' => 'nullable|exists:tickets,ticket_id',
            'organizer_id' => 'nullable|exists:organizers,organizer_id',
            'admin_id' => 'nullable|exists:admins,admin_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $event->update($request->all());
        return response()->json($event, 200);
    }
     /**
     * @OA\Delete(
     *     path="/api/events/{id}",
     *     tags={"Events"},
     *     summary="Delete an event",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Event ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Event deleted"),
     *     @OA\Response(response=404, description="Event not found")
     * )
     */
    public function destroy($id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        $event->delete();
        return response()->json(['message' => 'Event deleted successfully'], 200);
    }
}
