<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


     public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    public static function boot(){
        parent::boot();

        static::creating(function($user){
            $user->activation_token=str_random(30);
        });

    }

    public function statuses(){
        return $this->hasMany(Status::class);
    }


    public function feed(){
        // return $this->statuses()
        //         ->orderBy('created_at','desc');
        
        $user_ids=$this->followings->pluck('id')->toArray();
        array_push($user_ids,$this->id);
        return Status::whereIn('user_id',$user_ids)->with('user')->orderBy('created_at','desc');
    }

    public function followers(){
        //一个用户对应多个粉丝
        return $this->belongsToMany(User::class,'followers','user_id','follower_id');
    }

    public function followings(){
        //一个粉丝对应多个用户
        return $this->belongsToMany(User::class,"followers","follower_id","user_id");
    }

    //关注谁。。。。
    public function follow($user_ids){
        if(!is_array($user_ids)){
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids,false);
    }

    //取消关注谁
    public function unfollow($user_ids){
        if(!is_array($user_ids)){
            $user_ids=compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    //判断当前实例是否关注了谁
    public function isFollowing($user_id){
        return $this->followings->contains($user_id);
    }
}
