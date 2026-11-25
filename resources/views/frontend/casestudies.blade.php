<div class="modal fade csModal" id="csModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header justify-content-end">
                <button type="button" class="btn btn-outline-secondary rounded-circle" id="prevBtn">←</button>
                <span id="counter" class="px-2 text-dark">1 of 8</span>
                <button type="button" class="btn btn-outline-secondary rounded-circle me-2" id="nextBtn">→</button>
                <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start p-5">
                @foreach ($casestudies as $data)
                    <div id="modal-content_{{ $data['id'] }}" class="csModal-modal-content" style="display: none">
                        @if ($data['id'] == 'cs_1')
                            <h3 class="mb-3">{!! $data['title'] !!}</h3>
                            <div class="mb-3">Alliance Virtual Office offers virtual offices to the small businesses
                                who can't afford to have
                                physical locations in
                                different parts of the country in the US.</div>
                            <ul class="mb-3">
                                <li>Links Desired: 15 links per month for over 15 months & counting</li>
                                <li> Requirement: Get links for all their virtual office location category page</li>
                                <li> Criteria: Domain 60+, min traffic 10000+ in the US</li>
                                <li> Anchors: Virtual office, rent a virtual office, get a virtual office</li>
                            </ul>
                            <h5 class="mb-2">My role:</h5>
                            <ul class="mb-4">
                                <li>1. Filter the sites as per requirement</li>
                                <li>2. Generate the orders</li>
                                <li>3. Find the contextual texts to make links look natural</li>
                                <li>4. Acquire the link</li>
                                <li>5. Repeat</li>
                            </ul>
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <div class="result-title">DR</div>
                                    <div class="text-dark fw-bold">66 to 71</div>
                                </div>
                                <div>
                                    <div class="result-title">Traffic</div>
                                    <div class="text-dark fw-bold">28k to 41k</div>
                                </div>
                                <div>
                                    <div class="result-title">Traffic Value</div>
                                    <div class="text-dark fw-bold">125k$ to 150k$</div>
                                </div>
                            </div>
                            <img class="img-fluid mb-5"
                                src="{{ asset('frontend/casestudy/' . $data['id'] . '.png') }}" />
                            <video id="video_player_{{ $data['id'] }}" class="video-js vjs-default-skin w-100"
                                controls preload="auto" width="100%" height="360"
                                data-setup='{"playbackRates": [0.5, 1, 1.25, 1.5, 2],"preferFullWindow":true,"responsive":true}'>
                                <source src="{{ asset('frontend/videos/case_study/' . $data['video_link']) }}"
                                    type="video/mp4" />
                                Your browser does not support the video tag.
                            </video>
                        @elseif ($data['id'] == 'cs_2')
                            <h3 class="mb-3">{!! $data['title'] !!}</h3>
                            <div class="mb-3">Hostinger, a well-known hosting company based in Cyprus wanted
                                us to get
                                links to their statistics and landing pages.</div>
                            <ul class="mb-3">
                                <li>Desired links: 50+ links within 6 months and counting</li>
                                <li>Requirement: Get links for all their stat pages</li>
                                <li>Criteria: Domain 60+, Primary Traffic Source = USA, Page traffic > 100, Spam
                                    Score
                                    2%</li>
                            </ul>
                            <h5 class="mb-2">My role:</h5>
                            <ul class="mb-4">
                                <li>1. Filter the sites as per requirement</li>
                                <li>2. Generate the orders</li>
                                <li>3. Find the contextual texts to make links look natural</li>
                                <li>4. Acquire the link</li>
                                <li>5. Repeat</li>
                            </ul>
							<div class="d-flex justify-content-between mb-3">
                                <div>
                                    <div class="result-title">DR</div>
                                    <div class="text-dark fw-bold">90 to 91</div>
                                </div>
                                <div>
                                    <div class="result-title">Traffic</div>
                                    <div class="text-dark fw-bold">1.7M to 5.7M</div>
                                </div>
                                <div>
                                    <div class="result-title">Traffic Value</div>
                                    <div class="text-dark fw-bold">1M$ to 2M$</div>
                                </div>
                            </div>
                            <img class="img-fluid mb-5"
                                src="{{ asset('frontend/casestudy/' . $data['id'] . '.png') }}" />
                            <video id="video_player_{{ $data['id'] }}" class="video-js vjs-default-skin w-100"
                                controls preload="auto" width="100%" height="360"
                                data-setup='{"playbackRates": [0.5, 1, 1.25, 1.5, 2],"preferFullWindow":true,"responsive":true}'>
                                <source src="{{ asset('frontend/videos/case_study/' . $data['video_link']) }}"
                                    type="video/mp4" />
                                Your browser does not support the video tag.
                            </video>
                        @elseif ($data['id'] == 'cs_3')
                            <h3 class="mb-3">{!! $data['title'] !!}</h3>
                            <div class="mb-3">Freshworks CRM is a powerful, easy-to-use CRM solution
                                to unlock value across Sales, Marketing, and Customer Support
                                teams. They wanted to acquire SaaS links at a rapid pace.</div>
                            <ul class="mb-3">
                                <li>Desired links: 120+ links within the 3 months span</li>
                                <li>Requirement: Get links for all their CRM pages</li>
                                <li>Criteria: Domain 60+, min traffic 1000+, US traffic > 500, Page
                                    traffic > 10, Spam Score < 5.</li>
                            </ul>
                            <h5 class="mb-2">My role:</h5>
                            <ul class="mb-4">
                                <li>1. Filter the sites as per requirement</li>
                                <li>2. Generate the orders</li>
                                <li>3. Find the contextual texts to make links look natural</li>
                                <li>4. Acquire the link</li>
                                <li>5. Repeat</li>
                            </ul>
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <div class="result-title">DR</div>
                                    <div class="text-dark fw-bold">90 to 91</div>
                                </div>
                                <div>
                                    <div class="result-title">Traffic</div>
                                    <div class="text-dark fw-bold">400k to 420k</div>
                                </div>
                                <div>
                                    <div class="result-title">Traffic Value</div>
                                    <div class="text-dark fw-bold">1.4M$ to 1.8M$</div>
                                </div>
                            </div>
                            <img class="img-fluid mb-5"
                                src="{{ asset('frontend/casestudy/' . $data['id'] . '.png') }}" />
                            <video id="video_player_{{ $data['id'] }}" class="video-js vjs-default-skin w-100"
                                controls preload="auto" width="100%" height="360"
                                data-setup='{"playbackRates": [0.5, 1, 1.25, 1.5, 2],"preferFullWindow":true,"responsive":true}'>
                                <source src="{{ asset('frontend/videos/case_study/' . $data['video_link']) }}"
                                    type="video/mp4" />
                                Your browser does not support the video tag.
                            </video>
                        @elseif ($data['id'] == 'cs_4')
                            <h3 class="mb-3">{!! $data['title'] !!}</h3>
                            <div class="mb-3">Polo is a custom QR and dynamic QR code
                                generator that wants to acquire SaaS links, but isn’t
                                satisfied with the
                                existing context.</div>
                            <ul class="mb-3">
                                <li>Desired links: 500 links in over 11 months & counting</li>
                                <li>Requirement: Get links for their product page, branded
                                    links, listicle entries</li>
                                <li>Criteria: Domain 30+, min traffic 5000+, Page traffic > 1.
                                </li>
                                <li>Anchor: The QR Code Generator, Custom QR Code Generator</li>
                            </ul>
                            <h5 class="mb-2">My role:</h5>
                            <ul class="mb-4">
                                <li>1. Filter the sites as per requirement</li>
                                <li>2. Generate the orders</li>
                                <li>3. Find the contextual texts to make links look natural
                                </li>
                                <li>4. Acquire the link</li>
                                <li>5. Repeat</li>
                            </ul>
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <div class="result-title">DR</div>
                                    <div class="text-dark fw-bold">74 to 77</div>
                                </div>
                                <div>
                                    <div class="result-title">Traffic</div>
                                    <div class="text-dark fw-bold">1.2 Million to 1.4
                                        million</div>
                                </div>
                                <div>
                                    <div class="result-title">Traffic Value</div>
                                    <div class="text-dark fw-bold">maintained at 260k$</div>
                                </div>
                            </div>
                            <img class="img-fluid mb-5"
                                src="{{ asset('frontend/casestudy/' . $data['id'] . '.png') }}" />
                            <video id="video_player_{{ $data['id'] }}" class="video-js vjs-default-skin w-100"
                                controls preload="auto" width="100%" height="360"
                                data-setup='{"playbackRates": [0.5, 1, 1.25, 1.5, 2],"preferFullWindow":true,"responsive":true}'>
                                <source src="{{ asset('frontend/videos/case_study/' . $data['video_link']) }}"
                                    type="video/mp4" />
                                Your browser does not support the video tag.
                            </video>
                        @elseif ($data['id'] == 'cs_5')
                            <h3 class="mb-3">{!! $data['title'] !!}</h3>
                            <div class="mb-3">Science Soft is an IT consulting firm
                                that is mainly into building healthcare, Education,
                                Manufacturing, Finance
                                software. They had some of the toughest criteria in the
                                industry.</div>
                            <ul class="mb-3">
                                <li>Desired links: Built over 300 links in the past 36
                                    months</li>
                                <li>Requirement: Get links to all their development
                                    pages</li>
                                <li>New requirement: Get recommended on LLMs</li>
                                <li>Criteria: Domain Rating 30+, min traffic 500+, US
                                    traffic should be dominant, Page traffic > 10, Spam
                                    Score
                                    < 5, branded mention is a must</li>
                            </ul>
                            <h5 class="mb-2">My role:</h5>
                            <ul class="mb-4">
                                <li>1. Filter the sites as per requirement</li>
                                <li>2. Generate the orders</li>
                                <li>3. Find the contextual texts to make links look
                                    natural</li>
                                <li>4. Acquire the link</li>
                                <li>5. Repeat</li>
                            </ul>
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <div class="result-title">DR</div>
                                    <div class="text-dark fw-bold">76 to 77</div>
                                </div>
                                <div>
                                    <div class="result-title">Traffic</div>
                                    <div class="text-dark fw-bold">140k to 232k
                                    </div>
                                </div>
                                <div>
                                    <div class="result-title">Traffic Value</div>
                                    <div class="text-dark fw-bold">600k$ to 1M$
                                    </div>
                                </div>
                            </div>
                            <img class="img-fluid mb-5"
                                src="{{ asset('frontend/casestudy/' . $data['id'] . '.png') }}" />
                            <img class="img-fluid mb-5"
                                src="{{ asset('frontend/casestudy/' . $data['id'] . '_1.png') }}" />
                            <video id="video_player_{{ $data['id'] }}" class="video-js vjs-default-skin w-100"
                                controls preload="auto" width="100%" height="360"
                                data-setup='{"playbackRates": [0.5, 1, 1.25, 1.5, 2],"preferFullWindow":true,"responsive":true}'>
                                <source src="{{ asset('frontend/videos/case_study/' . $data['video_link']) }}"
                                    type="video/mp4" />
                                Your browser does not support the video tag.
                            </video>
                        @elseif ($data['id'] == 'cs_6')
                            <h3 class="mb-3">{!! $data['title'] !!}</h3>
                            <div class="mb-3">Advantage Club is an employee recognition platform that helps companies
                                reward achievements, foster appreciation, and boost productivity to improve retention.
                            </div>
                            <ul class="mb-3">
                                <li>Desired links: Built over 60 links in 4 months span</li>
                                <li>Requirement: Get links to all their home and service page</li>
                                <li>Criteria: Domain Rating > 50+, traffic > 10k</li>
                                <li>Anchor: Employee recognition software, rewards and recognition programs</li>
                            </ul>
                            <h5 class="mb-2">My role:</h5>
                            <ul class="mb-4">
                                <li>1. Filter the sites as per requirement</li>
                                <li>2. Generate the orders</li>
                                <li>3. Find the contextual texts to make links look
                                    natural</li>
                                <li>4. Acquire the link</li>
                                <li>5. Repeat</li>
                            </ul>
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <div class="result-title">DR</div>
                                    <div class="text-dark fw-bold">41 to 53</div>
                                </div>
                                <div>
                                    <div class="result-title">Traffic</div>
                                    <div class="text-dark fw-bold">19k to 65k
                                    </div>
                                </div>
                                <div>
                                    <div class="result-title">Traffic Value</div>
                                    <div class="text-dark fw-bold">3.3k$ to 10.5k$
                                    </div>
                                </div>
                            </div>
                            <img class="img-fluid mb-5"
                                src="{{ asset('frontend/casestudy/' . $data['id'] . '.png') }}" />
                            <video id="video_player_{{ $data['id'] }}" class="video-js vjs-default-skin w-100"
                                controls preload="auto" width="100%" height="360"
                                data-setup='{"playbackRates": [0.5, 1, 1.25, 1.5, 2],"preferFullWindow":true,"responsive":true}'>
                                <source src="{{ asset('frontend/videos/case_study/' . $data['video_link']) }}"
                                    type="video/mp4" />
                                Your browser does not support the video tag.
                            </video>
                        @elseif ($data['id'] == 'cs_7')
                            <h3 class="mb-3">{!! $data['title'] !!}</h3>
                            <div class="mb-3">Prospeo is an email finder and verifier tool. They wanted to acquire
                                contextual SaaS links and rank for their product pages such as email finder and
                                verifier.
                            </div>
                            <ul class="mb-3">
                                <li>Desired links: 60+ within 6 months span</li>
                                <li>Requirement: Get links for all their service and BoFU pages</li>
                                <li>Criteria: Domain 60+, min traffic 5000+, US traffic > 500, Page traffic > 10, Spam
                                    Score
                                    < 5. </li>
                            </ul>
                            <h5 class="mb-2">My role:</h5>
                            <ul class="mb-4">
                                <li>1. Filter the sites as per requirement</li>
                                <li>2. Generate the orders</li>
                                <li>3. Find the contextual texts to make links look
                                    natural</li>
                                <li>4. Acquire the link</li>
                                <li>5. Repeat</li>
                            </ul>
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <div class="result-title">DR</div>
                                    <div class="text-dark fw-bold">25 to 53</div>
                                </div>
                                <div>
                                    <div class="result-title">Traffic</div>
                                    <div class="text-dark fw-bold">800 to 8500
                                    </div>
                                </div>
                                <div>
                                    <div class="result-title">Traffic Value</div>
                                    <div class="text-dark fw-bold">1k$ to 8k$
                                    </div>
                                </div>
                            </div>
                            <img class="img-fluid mb-5"
                                src="{{ asset('frontend/casestudy/' . $data['id'] . '.png') }}" />
                            <video id="video_player_{{ $data['id'] }}" class="video-js vjs-default-skin w-100"
                                controls preload="auto" width="100%" height="360"
                                data-setup='{"playbackRates": [0.5, 1, 1.25, 1.5, 2],"preferFullWindow":true,"responsive":true}'>
                                <source src="{{ asset('frontend/videos/case_study/' . $data['video_link']) }}"
                                    type="video/mp4" />
                                Your browser does not support the video tag.
                            </video>
                        @elseif ($data['id'] == 'cs_8')
                            <h3 class="mb-3">{!! $data['title'] !!}</h3>
                            <div class="mb-3">CMS Minds is a dedicated WordPress design & development company that
                                also
                                deals in custom design, development, migration & maintenance.

                            </div>
                            <ul class="mb-3">
                                <li>Desired links: Built over 60 links in 11 months span</li>
                                <li>Requirement: Get links to all their design and development pages</li>
                                <li>Criteria: Domain Rating > 50+, primary traffic source = USA</li>
                                <li>Anchor: WordPress Development Company, WordPress Design Agency</li>
                            </ul>
                            <h5 class="mb-2">My role:</h5>
                            <ul class="mb-4">
                                <li>1. Filter the sites as per requirement</li>
                                <li>2. Generate the orders</li>
                                <li>3. Find the contextual texts to make links look
                                    natural</li>
                                <li>4. Acquire the link</li>
                                <li>5. Repeat</li>
                            </ul>
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <div class="result-title">DR</div>
                                    <div class="text-dark fw-bold">41 to 65</div>
                                </div>
                                <div>
                                    <div class="result-title">Traffic</div>
                                    <div class="text-dark fw-bold">1.3k to 6.5k
                                    </div>
                                </div>
                                <div>
                                    <div class="result-title">Traffic Value</div>
                                    <div class="text-dark fw-bold">4.4k$ to 23k$
                                    </div>
                                </div>
                            </div>
                            <img class="img-fluid mb-5"
                                src="{{ asset('frontend/casestudy/' . $data['id'] . '.png') }}" />
                            <video id="video_player_{{ $data['id'] }}" class="video-js vjs-default-skin w-100"
                                controls preload="auto" width="100%" height="360"
                                data-setup='{"playbackRates": [0.5, 1, 1.25, 1.5, 2],"preferFullWindow":true,"responsive":true}'>
                                <source src="{{ asset('frontend/videos/case_study/' . $data['video_link']) }}"
                                    type="video/mp4" />
                                Your browser does not support the video tag.
                            </video>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
