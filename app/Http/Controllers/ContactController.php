<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * 显示表单
     *
     * @return View
     */
    public function showForm()
    {
        return view('blog.contact');
    }

    /**
     * Email the contact request
     *
     * @param ContactMeRequest $request
     * @return Redirect
     */
    public function sendContactInfo(Requests\ContactMeRequest $request)
    {
//        return $request->all();
        $data = $request->only('name', 'email', 'phone');
        $data['messageLines'] = explode("\n", $request->get('message'));

        /*
         * 当处理多个email时 比较耗时（因为必须等到email发送完毕后才返回response）
         * 这时直接采用mail的queue来发送email
         * （会自动加到queue里 然后后台异步执行 不必等待发送时间 即可直接返回response）
         *  将Mail::send(view，data, clourse)改为Mail::queue(view，data, clourse)
         * */
        Mail::queue('emails.contact', $data, function ($message) use ($data) {
            $message->subject('Blog Contact Form: '.$data['name'])
                ->to(config('blog.contact_email'))//发送到指定的收件人的email
                ->replyTo($data['email']);//发送成功后 回复email给发件人
        });

        /*
         * 无延迟的返回response 因为已经将email加入到了queue当中（不然得等待email的发送完毕）
         *  但是现在得手动的执行队列php artisan queue:work 不然发送不了email
         *  还可以采用自动的方式将queue自动的执行
         * */
        return back()
            ->withSuccess("Thank you for your message. It has been sent.");
    }
}
