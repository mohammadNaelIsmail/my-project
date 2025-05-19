<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     schema="Event",
 *     type="object",
 *     title="Event Model",
 *     required={"event_id", "title", "date", "location"},
 *     @OA\Property(property="event_id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Music Festival"),
 *     @OA\Property(property="description", type="string", nullable=true, example="An outdoor music festival."),
 *     @OA\Property(property="date", type="string", format="date", example="2025-06-01"),
 *     @OA\Property(property="location", type="string", example="Central Park"),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 *)
 */
class Event extends Model{
    protected $table='event';
    protected $primaryKey='event_id';
    protected $fillable=[
        'location',
        'title',
        'type',
        'category',
        'description',
        'event_img',
        'revenue',
        'start_day',
        'end_day',
        'start_hour',
        'end_hour',
    'ticket_id',
    'organizer_id',
    'admin_id'];
    public $timestamps = false;
    public $incrementing = true;
   /*
    public function ticket(){
        return $this->belongsTo(ticket::class,'ticket_id');
    }
    public function organizer(){
        return $this->belongsTo(organizer::class,'organizer_id');
    }
    public function admin(){
        return $this->belongsTo(admin::class,'admin_id');
    }
 */
 }
