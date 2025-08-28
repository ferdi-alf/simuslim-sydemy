@extends('layouts.auth-layouts')

@section('content')
    <div class="min-h-screen bg-gradient-to-br flex justify-center items-center p-4">
        <div class="max-w-md w-full bg-slate-200/75 backdrop-blur-2xl rounded-2xl shadow-2xl overflow-hidden">
            <div class="">
                <div class="p-8 flex flex-col justify-center">
                    <div class="w-full mx-auto">
                        <div class="w-full text-center space-x-1.5">

                            <h1
                                class="font-extrabold text-center sm:text-3xl text-2xl text-transparent bg-clip-text bg-gradient-to-br from-indigo-400 via-indigo-500 to-indigo-400">
                                SyMuslim</h1>
                        </div>


                        <form class="space-y-4" method="POST">
                            @csrf

                            <div>
                                <x-fragments.text-field id="nameOrEmail" name="nameOrEmail" type="text"
                                    label="Name atau Email" placeholder="Masukan Name atau Email" :value="old('nameOrEmail')"
                                    :error="$errors->first('nameOrEmail')" required />
                            </div>

                            <div>
                                <x-fragments.text-field id="password" name="password" type="password" label="Password"
                                    placeholder="Masukan Password" :value="old('password')" :error="$errors->first('password')" required />
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input id="remember-me" name="remember" type="checkbox"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        {{ old('remember') ? 'checked' : '' }} />
                                    <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                                        Remember me
                                    </label>
                                </div>
                            </div>



                            <x-fragments.gradient-button type="submit" color="indigo" id="submit-btn">
                                Submit
                            </x-fragments.gradient-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
