@extends('layouts.admin')

@section('header')
    <div>
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">MCP / AI Agent</h2>
        <p class="text-sm text-gray-500">Bearer token untuk mengendalikan kos lewat AI agent.</p>
    </div>
@endsection

@section('content')
    @include('partials.mcp-panel', ['routePrefix' => 'admin', 'tokens' => $tokens, 'mcpUrl' => $mcpUrl])
@endsection
