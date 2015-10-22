@extends('layouts.master')

@section('title')
    Broadcast Call
@endsection

@section('content')
    @include('_messages')
    <h1>Broadcast a message</h1>
    <form action="/broadcast/send" method="POST" class="form-horizontal">
        <h3>1. Select a Recording</h3>
            <div class="recording-group">
                <div class="form-group">
                    <div class="col-sm-5">
                        <select class="form-control" id="selectRecordings">
                            <option>Select a Recording</option>
                        </select>
                        <p class="recording-status"></p>
                    </div>
                    <a href="#" class="preview-btn col-sm-2">
                        <i class="fa fa-play-circle fa-2x"></i> Preview
                        <audio id="recording-audio" src="" preload="auto"></audio>
                    </a>
                    <input type="textfield" class="hidden" id="recording-url" name="recording_url">
                </div>
                <a href="#" class="show-make"><i class="fa fa-angle-down"></i>  Make a new Recording</a>
                <div class="form-group make-recording">
                    <div class="col-sm-5">
                        <input type="textfield" class="form-control col-sm-4" id="recordingNumber" placeholder="Enter your phone number">
                    </div>
                    <a href="#" class="call-me col-sm-4 btn btn-square">Make a Recording</a>
                    <p class="recording-status help-block"></p>
                </div>
            </div>
            <h3>2. Enter a list of phone numbers</h3>
            <div class="form-group">
                <div class="col-sm-5">
                    <textarea class="form-control" id="phone-numbers" name="numbers" placeholder="Enter a comma separated list. Example: 5558675309, 2065554420" rows="5" ></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-square">Submit</button>
    </form>
@endsection('content')
